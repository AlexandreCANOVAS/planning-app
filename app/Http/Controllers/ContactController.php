<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Envoyer l'email en utilisant le template HTML
        Mail::send('emails.contact.message', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'messageContent' => $validated['message']
        ], function ($message) use ($validated) {
            $message->to('contact@planningapp.com')
                ->subject("[Contact Site Web] {$validated['subject']}")
                ->replyTo($validated['email'], $validated['name']);
        });

        return back()->with('success', 'Votre message a été envoyé avec succès !');
    }
}
