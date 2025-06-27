<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeInvitation;
use App\Models\Employe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function showAcceptanceForm(string $token)
    {
        $invitation = EmployeeInvitation::where('token', $token)->first();

        // Vérifier si l'invitation existe et n'a pas expiré
        if (!$invitation || $invitation->expires_at < Carbon::now()) {
            // Gérer le cas d'une invitation invalide ou expirée
            return redirect()->route('login')->with('error', 'Le lien d\'invitation est invalide ou a expiré.');
        }

        return view('auth.invitation', ['invitation' => $invitation]);
    }

    public function processAcceptance(Request $request)
    {
        // Journalisation de la requête pour le débogage
        \Illuminate\Support\Facades\Log::info('Tentative de traitement d\'invitation', [
            'token' => $request->input('token'),
            'has_password' => !empty($request->input('password')),
            'has_terms' => $request->has('terms'),
            'request_url' => $request->fullUrl(),
            'request_path' => $request->path(),
            'app_url' => config('app.url'),
        ]);
        
        try {
            $validated = $request->validate([
                'token' => 'required|string|exists:employee_invitations,token',
                'password' => 'required|string|min:8|confirmed',
                'terms' => 'required|accepted',
            ]);
            
            \Illuminate\Support\Facades\Log::info('Validation réussie');
            
            $invitation = EmployeeInvitation::where('token', $validated['token'])->first();
            
            if (!$invitation) {
                \Illuminate\Support\Facades\Log::error('Invitation non trouvée pour le token: ' . $validated['token']);
                return redirect(url('/login'))->with('error', 'Le lien d\'invitation est invalide.');
            }
            
            if ($invitation->expires_at < Carbon::now()) {
                \Illuminate\Support\Facades\Log::error('Invitation expirée pour le token: ' . $validated['token']);
                return redirect(url('/login'))->with('error', 'Le lien d\'invitation a expiré.');
            }
            
            \Illuminate\Support\Facades\Log::info('Invitation valide, début de la transaction');
            
            DB::beginTransaction();
            try {
                // 1. Créer le compte User
                $user = User::create([
                    'name' => $invitation->prenom . ' ' . $invitation->nom,
                    'email' => $invitation->email,
                    'password' => Hash::make($validated['password']),
                    'role' => 'employe', // Assigner explicitement le rôle 'employe'
                    'societe_id' => $invitation->societe_id,
                    'email_verified_at' => now(),
                    'password_changed' => true,
                ]);
                
                \Illuminate\Support\Facades\Log::info('Utilisateur créé avec ID: ' . $user->id);
                
                // 2. Créer l'enregistrement Employe
                $employeData = [
                    'nom' => $invitation->nom,
                    'prenom' => $invitation->prenom,
                    'email' => $invitation->email,
                    'societe_id' => $invitation->societe_id,
                    'user_id' => $user->id,
                    // Les autres champs seront à remplir par l'employé plus tard
                ];
                
                // Journaliser les données de l'employé avant création
                \Illuminate\Support\Facades\Log::info('Tentative de création d\'employé avec les données:', $employeData);
                
                $employe = Employe::create($employeData);
                
                \Illuminate\Support\Facades\Log::info('Employé créé avec ID: ' . $employe->id);
                
                // 3. Supprimer l'invitation
                $invitation->delete();
                
                DB::commit();
                \Illuminate\Support\Facades\Log::info('Transaction validée');
                
                // 4. Connecter l'utilisateur
                Auth::login($user);
                \Illuminate\Support\Facades\Log::info('Utilisateur connecté');
                
                // Régénérer la session pour des raisons de sécurité
                $request->session()->regenerate();
                
                // Redirection avec un message de succès - utiliser URL absolue pour éviter les problèmes de sous-dossier
                $homeUrl = url('/home');
                \Illuminate\Support\Facades\Log::info('Redirection vers: ' . $homeUrl);
                return redirect($homeUrl)->with('success', 'Bienvenue ! Votre compte a été créé et vous êtes maintenant connecté.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('Erreur lors de la création du compte: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->with('error', 'Une erreur est survenue lors de la création de votre compte : ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Erreur de validation: ', [
                'errors' => $e->errors(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur inattendue: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur inattendue est survenue: ' . $e->getMessage());
        }
    }
}
