@extends('emails.layouts.modern')

@section('content')
<h2 style="color: #4f46e5; margin-bottom: 20px; font-size: 22px;">Nouveau message de contact</h2>

<p>Un nouveau message a été envoyé depuis le formulaire de contact du site web.</p>

<div class="info-box">
    <h3>Détails du message</h3>
    
    <div class="detail-row">
        <div class="detail-label">Nom</div>
        <div class="detail-value">{{ $name }}</div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label">Email</div>
        <div class="detail-value">{{ $email }}</div>
    </div>
    
    <div class="detail-row">
        <div class="detail-label">Sujet</div>
        <div class="detail-value">{{ $subject }}</div>
    </div>
</div>

<div style="margin-top: 25px;">
    <h3 style="color: #4f46e5; font-size: 18px;">Message</h3>
    <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; margin-top: 10px;">
        {{ $messageContent }}
    </div>
</div>

<div class="mt-4">
    <p>Vous pouvez répondre directement à cet email pour contacter l'expéditeur.</p>
</div>

<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
    <p style="color: #6b7280; font-size: 14px;">Ce message a été envoyé le {{ date('d/m/Y à H:i') }}</p>
</div>
@endsection
