<?php

use App\Http\Controllers\{
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
    TarifController
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

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

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
});

// Routes pour le changement de mot de passe (doivent être définies avant les autres routes authentifiées)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [PlanningController::class, 'calendarIndex'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
            Route::get('/formations', [EmployeController::class, 'formations'])->name('employes.formations');
        });
        
        // Routes des lieux de travail
        Route::resource('lieux', LieuController::class);
        
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
            Route::get('/{conge}', [CongeController::class, 'show'])->name('conges.show');
            Route::get('/{conge}/edit', [CongeController::class, 'edit'])->name('conges.edit');
            Route::put('/{conge}', [CongeController::class, 'update'])->name('conges.update');
            Route::delete('/{conge}', [CongeController::class, 'destroy'])->name('conges.destroy');
            Route::patch('/{conge}/status', [CongeController::class, 'updateStatus'])->name('conges.update-status');
            Route::get('/validation', [CongeController::class, 'validation'])->name('conges.validation');
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

        // Routes pour les exports
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/plannings', [ExportController::class, 'exportPlannings'])->name('plannings');
            Route::get('/compta', [ExportController::class, 'exportCompta'])->name('compta');
            Route::get('/comptabilite', [ExportController::class, 'exportComptabilite'])->name('comptabilite');
        });
    });

    // Routes pour les employés
    Route::middleware(CheckEmploye::class)->prefix('employe')->name('employe.')->group(function () {
        // Routes des plannings
        Route::get('/plannings', [PlanningController::class, 'index'])->name('plannings.index');
        Route::get('/plannings/calendar', [PlanningController::class, 'employeCalendar'])->name('plannings.calendar');
        Route::get('/plannings/download-pdf', [PlanningController::class, 'exportPdfEmploye'])->name('plannings.download-pdf');
        Route::get('/plannings/collegue/{employe}/calendar', [PlanningController::class, 'voirPlanningCollegueCalendar'])->name('plannings.collegue');
        Route::post('/plannings/export-pdf-employe', [PlanningController::class, 'exportPdfEmploye'])->name('plannings.export-pdf-employe');
        
        // Routes des congés
        Route::prefix('conges')->name('conges.')->group(function () {
            Route::get('/', [CongeController::class, 'mesConges'])->name('index');
            Route::get('/calendar', [CongeController::class, 'calendar'])->name('calendar');
            Route::get('/events', [CongeController::class, 'getCongesEvents'])->name('events');
            Route::post('/demande', [CongeController::class, 'demandeConge'])->name('demande');
            Route::delete('/{conge}/annuler', [CongeController::class, 'annulerConge'])->name('annuler');
        });
        
        // Route pour l'export du planning mensuel employé
        Route::post('/plannings/export-mensuel', [PlanningController::class, 'exportMensuel'])->name('plannings.export');
    });

    // Routes communes
    Route::get('/export/plannings', [ExportController::class, 'exportPlannings'])->name('export.plannings');
    
    // Routes du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les formations
    Route::resource('formations', FormationController::class);

    // Notifications
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

require __DIR__.'/auth.php';
