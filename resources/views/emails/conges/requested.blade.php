@extends('emails.layouts.main')

@section('content')
    <h2>Bonjour {{ $manager->name }},</h2>
    
    <p>Une nouvelle demande de congé a été soumise et nécessite votre attention.</p>
    
    <div class="info-box">
        <h3>Détails de la demande :</h3>
        <p><strong>Employé :</strong> {{ $employee->prenom }} {{ $employee->nom }}</p>
        <p><strong>Période :</strong> {{ $conge->date_debut->format('d/m/Y') }} au {{ $conge->date_fin->format('d/m/Y') }}</p>
        <p><strong>Nombre de jours :</strong> {{ $conge->duree }}</p>
        @if($conge->motif)
            <p><strong>Motif :</strong> {{ $conge->motif }}</p>
        @endif
    </div>
    
    <p>Veuillez examiner cette demande et prendre une décision en cliquant sur le bouton ci-dessous :</p>
    
    <div class="text-center">
        <a href="{{ $url }}" class="btn">Traiter la demande</a>
    </div>
    
    <p class="mt-4">Merci de traiter cette demande dans les plus brefs délais.</p>
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
