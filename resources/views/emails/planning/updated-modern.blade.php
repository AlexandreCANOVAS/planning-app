@extends('emails.layouts.modern')

@section('content')
    <h2>Bonjour {{ $employee->name }},</h2>
    
    <p>Votre planning a été mis à jour. Vous trouverez ci-dessous les détails de votre planning modifié et une copie complète en pièce jointe.</p>
    
    <div class="calendar-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21 10.12h-8.17l-.59-.65L11.5 8.62h-.25l-.52.14L9 10.12H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm0 10H3v-8h5.08l.6-.65.73-.85h5.18l.73.85.6.65H21v8zm-3.5-3.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5z"/>
        </svg>
    </div>
    
    <div class="info-box">
        <h3>Détails du planning mis à jour</h3>
        
        <div class="detail-row">
            <span class="detail-label">Date :</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Lieu :</span>
            <span class="detail-value">{{ $planning->lieu ? $planning->lieu->nom : 'Non spécifié' }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Période :</span>
            <span class="detail-value">{{ ucfirst($planning->periode) }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Horaires :</span>
            <span class="detail-value">{{ $planning->heure_debut }} - {{ $planning->heure_fin }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Heures travaillées :</span>
            <span class="detail-value">{{ $planning->heures_travaillees }} h</span>
        </div>
    </div>
    
    <div class="text-center">
        <a href="http://127.0.0.1:8000/employe/plannings/calendar" class="btn">Voir mon planning complet</a>
    </div>
    
    <div class="attachment-info">
        <div class="attachment-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#4f46e5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
            </svg>
        </div>
        <div class="attachment-text">
            Votre planning mis à jour est disponible en pièce jointe au format PDF.
        </div>
    </div>
    
    <p class="mt-4">Si vous avez des questions concernant ces modifications, n'hésitez pas à contacter votre responsable.</p>
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
