<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un email de test pour vérifier la configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email') ?: config('mail.from.address');
        
        $this->info("Envoi d'un email de test à {$email}");
        
        try {
            Mail::raw('Ceci est un email de test pour vérifier la configuration de votre application Laravel.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test de configuration email');
            });
            
            $this->info('Email envoyé avec succès! Vérifiez votre boîte Mailtrap.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            return 1;
        }
    }
}
