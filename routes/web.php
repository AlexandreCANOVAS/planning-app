<?php

use App\Http\Controllers\{
    ThemeController,
    ProfileController,
    SocieteController,
    EmployeController,
    EmployeProfileController,
    EmployeDocumentsController,
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
    PageController,
    EchangeController,
    SoldeCongeController,
    GDPRController,
    InvitationController,
    TwoFactorAuthController
};

use App\Http\Controllers\Admin\DocumentController;

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
Route::get('/politique-de-confidentialite', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/politique-de-cookies', [PageController::class, 'cookies'])->name('pages.cookies');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/tarifs', function () {
    return view('pricing');
})->name('pricing');

Route::get('/a-propos', function () {
    return view('about');
})->name('about');



// Routes des fonctionnalités
Route::prefix('fonctionnalites')->name('features.')->group(function () {
    Route::get('/planning', function () {
        return view('features.planning');
    })->name('planning');
    
    Route::get('/conges', function () {
        return view('features.conges');
    })->name('conges');
    
    Route::get('/suivi-temps', function () {
        return view('features.temps');
    })->name('temps');
});

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

// Route de test pour Livewire
Route::get('/test-livewire', function () {
    return view('livewire-test');
})->name('test-livewire');

// Routes pour l'authentification à deux facteurs personnalisée
Route::middleware(['auth'])->group(function () {
    Route::get('/user/two-factor-auth', [TwoFactorAuthController::class, 'show'])
        ->name('two-factor.show');
        
    // Routes de confirmation de mot de passe pour 2FA
    Route::get('/user/two-factor-auth/confirm-password', [\App\Http\Controllers\Auth\ConfirmPassword2FAController::class, 'show'])
        ->name('two-factor.confirm-password');
    Route::post('/user/two-factor-auth/confirm-password', [\App\Http\Controllers\Auth\ConfirmPassword2FAController::class, 'confirm']);
        
    // Routes protégées par confirmation de mot de passe
    Route::post('/user/two-factor-auth/enable', [TwoFactorAuthController::class, 'enable'])
        ->middleware(['password.confirm'])
        ->name('two-factor.custom.enable');
    Route::post('/user/two-factor-auth/disable', [TwoFactorAuthController::class, 'disable'])
        ->middleware(['password.confirm'])
        ->name('two-factor.custom.disable');
    Route::post('/user/two-factor-auth/recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])
        ->middleware(['password.confirm'])
        ->name('two-factor.custom.recovery-codes');
    
    // Routes pour la vérification 2FA
    Route::get('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'show'])
        ->name('two-factor.verify');
    Route::post('/two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'verify']);
    Route::post('/two-factor-challenge-recovery', [\App\Http\Controllers\Auth\TwoFactorVerificationController::class, 'verifyRecoveryCode'])
        ->name('two-factor.verify-recovery');
});

// Routes pour l'invitation des employés
Route::get('invitation/accept/{token}', [InvitationController::class, 'showAcceptanceForm'])->name('employee.invitation.accept');
Route::post('invitation/accept', [InvitationController::class, 'processAcceptance'])->name('employee.invitation.process');

// Les routes d'authentification (login, register, logout, etc.) sont gérées par le fichier routes/auth.php

// Routes nécessitant une authentification et une vérification d'email
Route::middleware(['auth', 'verified'])->group(function () {
    // Le groupe 'verified' implique 'auth', donc on peut tout mettre ici.
    // Laravel redirigera automatiquement vers la page de vérification si l'email n'est pas vérifié.

    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route pour changer le thème
    Route::post('/theme/toggle', [ThemeController::class, 'toggleTheme'])->name('theme.toggle');

    // Routes pour le profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route pour l'export des données RGPD
    Route::get('/gdpr/export', [GDPRController::class, 'exportData'])->name('gdpr.export');

    // Routes pour le changement de mot de passe
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

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
    // Le middleware 'auth' est redondant ici car déjà appliqué au groupe parent
    Route::middleware(CheckEmployeur::class)->group(function () {
        // Routes pour les documents (admin)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('documents', DocumentController::class);
            Route::get('documents/{document}/manage-employes', [DocumentController::class, 'manageEmployes'])->name('documents.manage-employes');
            Route::post('documents/{document}/update-employes', [DocumentController::class, 'updateEmployes'])->name('documents.update-employes');
            Route::get('documents/stats', [DocumentController::class, 'stats'])->name('documents.stats');
            
            // Routes pour les catégories de documents
            Route::resource('document-categories', DocumentCategoryController::class);
            Route::get('get-document-categories', [DocumentCategoryController::class, 'getCategories'])->name('document-categories.get');
        });
        
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
            Route::get('/export-pdf/{employe_id}/{mois}/{annee}', [PlanningController::class, 'exportPDF'])->name('plannings.export-pdf');
            Route::post('/export-pdf-with-modifications', [PlanningController::class, 'exportPdfWithModifications'])->name('plannings.export-pdf-with-modifications');
            
            Route::get('/create-monthly-calendar', [PlanningController::class, 'createMonthlyCalendar'])->name('plannings.create-monthly-calendar');
            
                        Route::get('/day-details/{date}', [PlanningController::class, 'getDayDetails'])->name('plannings.dayDetails');
            
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
            Route::delete('/supprimer-conge/{conge_id}', [PlanningController::class, 'supprimerConge'])
                ->name('plannings.supprimer-conge');
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
        
        // Routes de gestion des documents
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::get('/create', [DocumentController::class, 'create'])->name('create');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::get('/stats', [DocumentController::class, 'stats'])->name('stats');
            Route::get('/{id}', [DocumentController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [DocumentController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DocumentController::class, 'update'])->name('update');
            Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/manage-employes', [DocumentController::class, 'manageEmployes'])->name('manage-employes');
        });

        // Routes pour les exports
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/plannings', [ExportController::class, 'exportPlannings'])->name('plannings');
            Route::get('/compta', [ExportController::class, 'exportCompta'])->name('compta');
            Route::get('/comptabilite', [ExportController::class, 'exportComptabilite'])->name('comptabilite');
            Route::get('/comptabilite/excel', [ExportController::class, 'exportComptabiliteExcel'])->name('comptabilite.excel');
        });
    });

    // Routes API pour la carte interactive
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/lieux', [\App\Http\Controllers\Api\LieuController::class, 'index'])->name('lieux.index');
        Route::get('/lieux/{lieu}', [\App\Http\Controllers\Api\LieuController::class, 'show'])->name('lieux.show');
    });

    // Routes pour les employés
    Route::middleware(CheckEmploye::class)->prefix('employe')->name('employe.')->group(function () {
        // Route du profil employé en lecture seule
        Route::get('/profile', [EmployeProfileController::class, 'show'])->name('profile.show');
        
        // Routes des documents employé
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [EmployeDocumentsController::class, 'index'])->name('index');
            Route::get('/{id}', [EmployeDocumentsController::class, 'show'])->name('show');
            Route::get('/{id}/preview', [EmployeDocumentsController::class, 'preview'])->name('preview');
            Route::get('/{id}/download', [EmployeDocumentsController::class, 'download'])->name('download');
            Route::post('/{id}/confirm', [EmployeDocumentsController::class, 'confirmLecture'])->name('confirm');
        });
        
        // Routes des formations
        Route::resource('formations', FormationController::class);
        Route::post('/formations/{formation}/upload-document', [FormationController::class, 'uploadDocument'])->name('formations.upload-document');
        Route::delete('/formations/{formation}/documents/{document}', [FormationController::class, 'deleteDocument'])->name('formations.delete-document');
        Route::post('/formations/{formation}/employes', [FormationController::class, 'updateEmployes'])->name('formations.update-employes');
        Route::get('/plannings', [PlanningController::class, 'index'])->name('plannings.index');
        Route::get('/plannings/calendar', [PlanningController::class, 'employeCalendar'])->name('plannings.calendar');
        Route::get('/plannings/download-pdf', [PlanningController::class, 'exportPdfEmploye'])->name('plannings.download-pdf');
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

Route::post('/cookie-consent', [\App\Http\Controllers\CookieConsentController::class, 'accept'])->name('cookie-consent.accept');

require __DIR__.'/auth.php';
