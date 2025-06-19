<?php

use App\Http\Controllers\{
    ThemeController,
    ProfileController,
    SocieteController,
    EmployeController,
    LieuController,
    PlanningController,
    CongeController,
    ExportController,
    NotificationController,
    DashboardController,
    ChangePasswordController,
    FormationController,
    ContactController,
    AuthenticatedSessionController,
    ComptabiliteController,
    TauxHeuresSupController,
    TarifController,
    EchangeController,
    SoldeCongeController
};

use App\Http\Controllers\Auth\{
    RegisteredUserController,
    ForgotPasswordController,
    ResetPasswordController,
    VerifyEmailController,
    EmailVerificationPromptController,
    EmailVerificationNotificationController,
    NewPasswordController,
    PasswordResetLinkController
};

use App\Http\Middleware\{
    CheckEmployeur,
    CheckEmploye
};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Route pour l'authentification WebSocket - doit être avant les autres routes
Broadcast::routes();

// Pages publiques
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/fonctionnalites', function () {
    return view('fonctionnalites');
})->name('fonctionnalites');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

Route::get('/mentions-legales', function () {
    return view('mentions-legales');
})->name('mentions-legales');

Route::get('/politique-confidentialite', function () {
    return view('politique-confidentialite');
})->name('politique-confidentialite');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('verify-email', [EmailVerificationPromptController::class, 'show'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');
    
    // Route pour changer le thème
    Route::post('/theme/toggle', [ThemeController::class, 'toggleTheme'])->name('theme.toggle');
    
    // Routes pour le profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour le changement de mot de passe (doivent être définies avant les autres routes authentifiées)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [PlanningController::class, 'calendarIndex'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes des notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    });
    
    // Routes d'export PDF accessibles à tous les utilisateurs authentifiés
    Route::get('/plannings/export-pdf/{employe_id}/{mois}/{annee}', [PlanningController::class, 'exportPdf'])
        ->name('plannings.export-pdf');
        
    // Routes d'export pour la comptabilité
    Route::get('/export/comptabilite', [ExportController::class, 'exportComptabilitePDF'])->name('export.comptabilite');
    Route::get('/export/comptabilite/excel', [ExportController::class, 'exportComptabiliteExcel'])->name('export.comptabilite.excel');
        
    // Routes pour les employeurs
    Route::middleware(['auth', CheckEmployeur::class])->group(function () {
        Route::resource('societes', SocieteController::class)->only(['create', 'store', 'edit', 'update']);
        Route::post('/societe/upload-logo', [SocieteController::class, 'uploadLogo'])->name('societe.upload-logo');
        
        // Routes des employés
        Route::prefix('employes')->group(function () {
            Route::get('/', [EmployeController::class, 'index'])->name('employes.index');
            Route::get('/create', [EmployeController::class, 'create'])->name('employes.create');
            Route::post('/', [EmployeController::class, 'store'])->name('employes.store');
            Route::get('/{employe}/edit', [EmployeController::class, 'edit'])->name('employes.edit');
            Route::put('/{employe}', [EmployeController::class, 'update'])->name('employes.update');
            Route::delete('/{employe}', [EmployeController::class, 'destroy'])->name('employes.destroy');
            Route::get('/{employe}/stats', [EmployeController::class, 'stats'])->name('employes.stats');
            Route::get('/{employe}/formations', [EmployeController::class, 'formations'])->name('employes.formations');
            Route::get('/{employe}/details', [EmployeController::class, 'show'])->name('employes.show');
        });
        
        // Routes des lieux de travail
        Route::get('/lieux', [LieuController::class, 'index'])->name('lieux.index');
        Route::get('/lieux/create', [LieuController::class, 'create'])->name('lieux.create');
        Route::post('/lieux', [LieuController::class, 'store'])->name('lieux.store');
        Route::get('/lieux/{lieu}/edit', [LieuController::class, 'edit'])->name('lieux.edit');
        Route::put('/lieux/{lieu}', [LieuController::class, 'update'])->name('lieux.update');
        Route::delete('/lieux/{lieu}', [LieuController::class, 'destroy'])->name('lieux.destroy');
        Route::delete('/lieux/{lieu}/force', [LieuController::class, 'forceDestroy'])->name('lieux.forceDestroy');
        
        // Nouvelles routes pour les actions rapides des lieux
        Route::get('/lieux/{lieu}/plannings', [LieuController::class, 'plannings'])->name('lieux.plannings');
        Route::get('/lieux/{lieu}/duplicate', [LieuController::class, 'duplicate'])->name('lieux.duplicate');
        Route::get('/lieux/export/pdf', [LieuController::class, 'exportPdf'])->name('lieux.export.pdf');
        Route::get('/lieux/export/excel', [LieuController::class, 'exportExcel'])->name('lieux.export.excel');
        
        // Routes des plannings
        Route::prefix('plannings')->group(function () {
            Route::get('/calendar', [PlanningController::class, 'calendar'])->name('plannings.calendar');
            Route::get('/download-pdf', [PlanningController::class, 'downloadPdf'])->name('plannings.download-pdf');
            Route::post('/export-pdf-with-modifications', [PlanningController::class, 'exportPdfWithModifications'])->name('plannings.export-pdf-with-modifications');
            Route::get('/create-monthly-calendar', [PlanningController::class, 'createMonthlyCalendar'])
                ->name('plannings.create-monthly-calendar');
            Route::get('/edit-monthly-calendar', [PlanningController::class, 'editMonthlyCalendar'])
                ->name('plannings.edit-monthly-calendar')
                ->withoutMiddleware([CheckEmployeur::class]);
            Route::post('/store-monthly', [PlanningController::class, 'storeMonthly'])
                ->name('plannings.store_monthly');
            Route::post('/store-monthly-calendar', [PlanningController::class, 'storeMonthlyCalendar'])
                ->name('plannings.store-monthly-calendar');
            Route::post('/update-monthly-calendar', [PlanningController::class, 'updateMonthlyCalendar'])
                ->name('plannings.update-monthly-calendar');
            Route::get('/get-monthly-calendar/{employe_id}/{annee}/{mois}', [PlanningController::class, 'getMonthlyCalendar'])
                ->name('plannings.get-monthly-calendar');
            Route::get('/view-monthly-calendar/{employe_id}/{mois}/{annee}', [PlanningController::class, 'viewMonthlyCalendar'])
                ->name('plannings.view-monthly-calendar')
                ->withoutMiddleware([CheckEmployeur::class]);
            Route::delete('/destroy-monthly/{employe_id}/{year_month}', [PlanningController::class, 'destroyMonthly'])
                ->name('plannings.destroy_monthly');
            Route::get('/destroy-monthly-confirm/{employe_id}/{year_month}', [PlanningController::class, 'destroyMonthlyConfirm'])
                ->name('plannings.destroy_monthly_confirm');
            Route::post('/remplir-jours-non-travailles/{employe_id}/{annee}/{mois}', [PlanningController::class, 'remplirJoursNonTravailles'])
                ->name('plannings.remplir-jours-non-travailles');
            Route::post('/ajouter-conge/{employe_id}/{annee}/{mois}', [PlanningController::class, 'ajouterConge'])
                ->name('plannings.ajouter-conge');
        });
        
        // Routes de la comptabilité
        Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite.index');
        Route::post('/comptabilite/calculer-heures', [ComptabiliteController::class, 'calculerHeures'])->name('comptabilite.calculer-heures');
        
        // Routes des congés pour employeur
        Route::prefix('conges')->group(function () {
            Route::get('/', [CongeController::class, 'index'])->name('conges.index');
            Route::get('/create', [CongeController::class, 'create'])->name('conges.create');
            Route::post('/', [CongeController::class, 'store'])->name('conges.store');
            Route::get('/calendar', [CongeController::class, 'adminCalendar'])->name('conges.calendar');
            Route::get('/calendar/events', [CongeController::class, 'getAdminEvents'])->name('conges.calendar.events');
            Route::get('/validation', [CongeController::class, 'validation'])->name('conges.validation');
            
            // Routes pour la gestion des soldes de congés
            Route::get('/solde', [SoldeCongeController::class, 'index'])->name('solde.index');
            Route::get('/solde/{employe}/edit', [SoldeCongeController::class, 'edit'])->name('solde.edit');
            Route::put('/solde/{employe}', [SoldeCongeController::class, 'update'])->name('solde.update');
            Route::get('/solde/{employe}/historique', [SoldeCongeController::class, 'historique'])->name('solde.historique');
            
            // Routes avec paramètres dynamiques (doivent être après les routes statiques)
            Route::get('/{conge}', [CongeController::class, 'show'])->name('conges.show');
            Route::get('/{conge}/edit', [CongeController::class, 'edit'])->name('conges.edit');
            Route::put('/{conge}', [CongeController::class, 'update'])->name('conges.update');
            Route::delete('/{conge}', [CongeController::class, 'destroy'])->name('conges.destroy');
            Route::patch('/{conge}/status', [CongeController::class, 'updateStatus'])->name('conges.update-status');
            Route::post('/{conge}/valider', [CongeController::class, 'valider'])->name('conges.valider');
            Route::post('/{conge}/refuser', [CongeController::class, 'refuser'])->name('conges.refuser');
        });
        
        // Routes pour la gestion des taux
        Route::get('/taux', [TauxHeuresSupController::class, 'index'])->name('taux.index');
        Route::post('/taux', [TauxHeuresSupController::class, 'store'])->name('taux.store');
        Route::put('/taux/{taux}', [TauxHeuresSupController::class, 'update'])->name('taux.update');
        Route::delete('/taux/{taux}', [TauxHeuresSupController::class, 'destroy'])->name('taux.destroy');

        // Routes pour les tarifs
        Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.index');
        Route::get('/tarifs/create', [TarifController::class, 'create'])->name('tarifs.create');
        Route::post('/tarifs', [TarifController::class, 'store'])->name('tarifs.store');
        Route::get('/tarifs/{tarif}/edit', [TarifController::class, 'edit'])->name('tarifs.edit');
        Route::put('/tarifs/{tarif}', [TarifController::class, 'update'])->name('tarifs.update');
        Route::delete('/tarifs/{tarif}', [TarifController::class, 'destroy'])->name('tarifs.destroy');

        // Routes pour les formations
        Route::resource('formations', FormationController::class);

        // Routes pour les exports
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/plannings', [ExportController::class, 'exportPlannings'])->name('plannings');
            Route::get('/compta', [ExportController::class, 'exportCompta'])->name('compta');
            Route::get('/comptabilite', [ExportController::class, 'exportComptabilite'])->name('comptabilite');
        });
    });

    // Routes API pour la carte interactive
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/lieux', [\App\Http\Controllers\Api\LieuController::class, 'index'])->name('lieux.index');
        Route::get('/lieux/{lieu}', [\App\Http\Controllers\Api\LieuController::class, 'show'])->name('lieux.show');
    });

    // Routes pour les employés
    Route::middleware(CheckEmploye::class)->prefix('employe')->name('employe.')->group(function () {
        // Routes des plannings
        Route::get('/plannings', [PlanningController::class, 'index'])->name('plannings.index');
        Route::get('/plannings/calendar', [PlanningController::class, 'employeCalendar'])->name('plannings.calendar');
        Route::get('/plannings/download-pdf', [PlanningController::class, 'exportPdfEmploye'])->name('plannings.download-pdf');
        Route::get('/plannings/{planning}', [PlanningController::class, 'show'])->name('plannings.show');
        Route::get('/plannings/collegue/{employe}/calendar', [PlanningController::class, 'voirPlanningCollegueCalendar'])->name('plannings.collegue');
        Route::get('/plannings/collegue/{employe}/compare', [PlanningController::class, 'comparerPlannings'])->name('plannings.comparer');
        Route::post('/plannings/export-pdf-employe', [PlanningController::class, 'exportPdfEmploye'])->name('plannings.export-pdf-employe');
        Route::post('/plannings/demande-modification', [PlanningController::class, 'demandeModification'])->name('plannings.demande-modification');
        Route::post('/plannings/proposer-echange', [PlanningController::class, 'proposerEchange'])->name('plannings.proposer-echange');
        Route::get('/plannings/echanges', [PlanningController::class, 'listeEchanges'])->name('plannings.liste-echanges');
        Route::post('/plannings/echanges/{echange}/accepter', [PlanningController::class, 'accepterEchange'])->name('plannings.accepter-echange');
        Route::post('/plannings/echanges/{echange}/refuser', [PlanningController::class, 'refuserEchange'])->name('plannings.refuser-echange');
        
        // Routes des échanges de planning
        Route::prefix('echanges')->name('echanges.')->group(function () {
            Route::get('/', [EchangeController::class, 'index'])->name('index');
            Route::get('/create', [EchangeController::class, 'create'])->name('create');
            Route::post('/', [EchangeController::class, 'store'])->name('store');
            Route::get('/{id}', [EchangeController::class, 'show'])->name('show');
            Route::post('/{id}/repondre', [EchangeController::class, 'repondre'])->name('repondre');
            Route::delete('/{id}/annuler', [EchangeController::class, 'annuler'])->name('annuler');
        });
        
        // Routes des congés
        Route::prefix('conges')->name('conges.')->group(function () {
            Route::get('/', [CongeController::class, 'indexEmploye'])->name('index');
            Route::get('/create', [CongeController::class, 'createEmploye'])->name('create');
            Route::post('/', [CongeController::class, 'storeEmploye'])->name('store');
            Route::post('/demande', [CongeController::class, 'demandeConge'])->name('demande');
            Route::get('/calendar', [CongeController::class, 'employeCalendar'])->name('calendar');
            Route::get('/calendar/events', [CongeController::class, 'getEmployeEvents'])->name('calendar.events');
            Route::get('/{conge}', [CongeController::class, 'showEmploye'])->name('show');
            Route::get('/{conge}/edit', [CongeController::class, 'editEmploye'])->name('edit');
            Route::put('/{conge}', [CongeController::class, 'updateEmploye'])->name('update');
            Route::delete('/{conge}', [CongeController::class, 'destroyEmploye'])->name('destroy');
            Route::delete('/{conge}/annuler', [CongeController::class, 'annulerConge'])->name('annuler');
        });
    });
});

// Routes de test pour les événements WebSocket et emails
Route::prefix('tests')->middleware(['auth'])->group(function () {
    // Route de test pour l'envoi d'emails
    Route::get('/email', function () {
        try {
            \Illuminate\Support\Facades\Mail::raw('Ceci est un email de test pour vérifier la configuration Mailtrap.', function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('Test de configuration email');
            });
            return 'Email envoyé avec succès! Vérifiez votre boîte Mailtrap.';
        } catch (\Exception $e) {
            return 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage();
        }
    });
    
    // Route de test pour l'envoi d'email de planning créé
    Route::get('/planning-email', function () {
        // Récupérer un planning existant et son employé
        $planning = App\Models\Planning::with('employe.user')->first();
        
        if (!$planning || !$planning->employe || !$planning->employe->user) {
            return 'Impossible de trouver un planning avec un employé et un utilisateur associé';
        }
        
        // Envoyer l'email directement (sans file d'attente)
        Mail::to($planning->employe->user->email)
            ->send(new App\Mail\PlanningCreated($planning, $planning->employe->user));
        
        return 'Email de planning envoyé directement à ' . $planning->employe->user->email . '. Vérifiez Mailtrap.';
    });
    
    // Route de test pour l'envoi d'email via la file d'attente
    Route::get('/planning-email-queue', function () {
        // Récupérer un planning existant et son employé
        $planning = App\Models\Planning::with('employe.user')->first();
        
        if (!$planning || !$planning->employe || !$planning->employe->user) {
            return 'Impossible de trouver un planning avec un employé et un utilisateur associé';
        }
        
        // Envoyer l'email via la file d'attente
        Mail::to($planning->employe->user->email)
            ->queue(new App\Mail\PlanningCreated($planning, $planning->employe->user));
        
        // Vérifier le nombre de jobs dans la file d'attente
        $jobCount = DB::table('jobs')->count();
        
        return 'Email de planning mis en file d\'attente pour ' . $planning->employe->user->email . '. ' . 
               $jobCount . ' job(s) dans la file d\'attente. Vérifiez Mailtrap après traitement par le worker.';
    });
    
    // Route de test pour l'envoi d'email moderne avec pièce jointe
    Route::get('/planning-email-modern', function () {
        $planning = \App\Models\Planning::with(['employe.user', 'lieu'])->first();
        
        if (!$planning || !$planning->employe || !$planning->employe->user) {
            return 'Impossible de trouver un planning avec un employé et un utilisateur associé';
        }
        
        Mail::to($planning->employe->user->email)
            ->send(new \App\Mail\PlanningCreated($planning, $planning->employe->user));
        
        return 'Email moderne de planning avec pièce jointe envoyé à ' . $planning->employe->user->email . '. Vérifiez Mailtrap.';
    });
    
    // Route de test pour l'envoi d'email de mise à jour de planning avec pièce jointe
    Route::get('/planning-email-updated', function () {
        $planning = \App\Models\Planning::with(['employe.user', 'lieu'])->first();
        
        if (!$planning || !$planning->employe || !$planning->employe->user) {
            return 'Impossible de trouver un planning avec un employé et un utilisateur associé';
        }
        
        Mail::to($planning->employe->user->email)
            ->send(new \App\Mail\PlanningUpdated($planning, $planning->employe->user));
        
        return 'Email de mise à jour de planning avec pièce jointe envoyé à ' . $planning->employe->user->email . '. Vérifiez Mailtrap.';
    });
    
    // Page de test WebSocket
    Route::get('/websocket/{id}', function ($id) {
        $employe = \App\Models\Employe::findOrFail($id);
        return view('tests.websocket', compact('employe'));
    })->name('tests.websocket');
    
    // Déclenchement d'un événement de test
    Route::get('/test-event/{id}', function ($id) {
        $employe = \App\Models\Employe::findOrFail($id);
        $historique = $employe->historiqueConges()->latest()->first();
        
        if (!$historique) {
            $historique = $employe->historiqueConges()->create([
                'user_id' => auth()->id(),
                'type_modification' => 'test',
                'ancien_solde_conges' => $employe->solde_conges,
                'nouveau_solde_conges' => $employe->solde_conges,
                'ancien_solde_rtt' => $employe->solde_rtt,
                'nouveau_solde_rtt' => $employe->solde_rtt,
                'ancien_solde_conges_exceptionnels' => $employe->solde_conges_exceptionnels,
                'nouveau_solde_conges_exceptionnels' => $employe->solde_conges_exceptionnels,
                'commentaire' => 'Test de notification en temps réel',
            ]);
        }
        
        event(new \App\Events\SoldeCongeModified($employe, $historique));
        
        return 'Evénement envoyé pour l\'employé ' . $employe->prenom . ' ' . $employe->nom;
    })->name('tests.event');
});

require __DIR__.'/auth.php';
