@extends('emails.layouts.main')

@section('content')
    <h2>Bonjour {{ $employee->name }},</h2>
    
    <p>Votre nouveau planning est maintenant disponible.</p>
    
    <div class="info-box">
        <h3>Détails du planning :</h3>
        <p><strong>Date :</strong> {{ $planning->date }}</p>
        <p><strong>Lieu :</strong> {{ $planning->lieu_id }}</p>
        <p><strong>Heures travaillées :</strong> {{ $planning->heures_travaillees }}</p>
    </div>
    
    <p>Vous pouvez consulter votre planning complet en vous connectant à l'application.</p>
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
