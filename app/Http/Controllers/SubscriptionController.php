<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    /**
     * Affiche la page d'abonnement
     */
    public function show()
    {
        return view('subscription.show', [
            'intent' => auth()->user()->createSetupIntent()
        ]);
    }

    /**
     * Traite la souscription à l'abonnement
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $user = $request->user();

        try {
            // Si l'utilisateur a déjà un abonnement actif, on le redirige
            if ($user->subscribed('default')) {
                return redirect()->route('dashboard')->with('info', 'Vous avez déjà un abonnement actif.');
            }

            // Créer ou récupérer le client Stripe
            $user->createOrGetStripeCustomer();

            // Créer l'abonnement
            $user->newSubscription('default', config('services.stripe.price_id'))
                ->create($request->payment_method);

            return redirect()->route('dashboard')->with('success', 'Votre abonnement a été souscrit avec succès !');
        } catch (CardException $e) {
            return back()->withErrors(['message' => 'Erreur de carte : ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'abonnement : ' . $e->getMessage());
            return back()->withErrors(['message' => 'Une erreur est survenue lors de la souscription.']);
        }
    }

    /**
     * Affiche la page de gestion d'abonnement
     */
    public function manage()
    {
        $user = auth()->user();
        
        return view('subscription.manage', [
            'subscription' => $user->subscription('default'),
            'invoices' => $user->invoices(),
            'paymentMethod' => $user->defaultPaymentMethod(),
        ]);
    }

    /**
     * Annule l'abonnement (à la fin de la période de facturation)
     */
    public function cancel()
    {
        $user = auth()->user();
        
        if ($user->subscription('default')->cancel()) {
            return back()->with('success', 'Votre abonnement sera annulé à la fin de la période de facturation.');
        }
        
        return back()->withErrors(['message' => 'Une erreur est survenue lors de l\'annulation.']);
    }

    /**
     * Reprend un abonnement annulé
     */
    public function resume()
    {
        $user = auth()->user();
        
        if ($user->subscription('default')->resume()) {
            return back()->with('success', 'Votre abonnement a été repris avec succès.');
        }
        
        return back()->withErrors(['message' => 'Une erreur est survenue lors de la reprise de l\'abonnement.']);
    }
}
