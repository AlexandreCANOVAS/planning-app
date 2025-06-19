@extends('emails.layouts.modern')

@section('content')
    <h2>Bonjour {{ $employee->name }},</h2>
    
    <p>Votre nouveau planning est maintenant disponible. Vous trouverez ci-dessous les détails de votre planning et une copie complète en pièce jointe.</p>
    
    <div class="calendar-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/>
        </svg>
    </div>
    
    <div class="info-box">
        <h3>Détails du planning</h3>
        
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
            Votre planning complet est disponible en pièce jointe au format PDF.
        </div>
    </div>
    
    <p class="mt-4">Si vous avez des questions concernant votre planning, n'hésitez pas à contacter votre responsable.</p>
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
