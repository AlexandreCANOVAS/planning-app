@extends('emails.layouts.main')

@section('content')
    <h2>Bonjour {{ $employee->name }},</h2>
    
    <p>Votre nouveau planning est maintenant disponible.</p>
    
    <div class="info-box">
        <h3>Détails du planning :</h3>
        <p><strong>Période :</strong> {{ $planning->start_date->format('d/m/Y') }} au {{ $planning->end_date->format('d/m/Y') }}</p>
        <p><strong>Lieu :</strong> {{ $planning->lieu->nom ?? 'Non spécifié' }}</p>
        <p><strong>Heures totales :</strong> {{ $planning->total_hours ?? 'À déterminer' }}</p>
    </div>
    
    <p>Vous pouvez consulter votre planning complet en cliquant sur le bouton ci-dessous :</p>
    
    <div class="text-center">
        <a href="{{ $url }}" class="btn">Voir mon planning</a>
    </div>
    
    <p class="mt-4">Si vous avez des questions concernant votre planning, n'hésitez pas à contacter votre responsable.</p>
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
