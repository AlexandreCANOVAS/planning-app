@extends('emails.layouts.main')

@section('content')
    <h2>Bonjour {{ $employee->name }},</h2>
    
    @if($status === 'approved')
        <p>Votre demande de congé a été <strong style="color: #10b981;">approuvée</strong>.</p>
    @elseif($status === 'rejected')
        <p>Votre demande de congé a été <strong style="color: #ef4444;">refusée</strong>.</p>
    @else
        <p>Le statut de votre demande de congé a été mis à jour.</p>
    @endif
    
    <div class="info-box">
        <h3>Détails de la demande :</h3>
        <p><strong>Période :</strong> {{ $conge->date_debut->format('d/m/Y') }} au {{ $conge->date_fin->format('d/m/Y') }}</p>
        <p><strong>Type de congé :</strong> {{ $conge->type }}</p>
        <p><strong>Nombre de jours :</strong> {{ $conge->nombre_jours }}</p>
        @if($conge->commentaire)
            <p><strong>Commentaire :</strong> {{ $conge->commentaire }}</p>
        @endif
    </div>
    
    <p>Vous pouvez consulter les détails de votre demande en cliquant sur le bouton ci-dessous :</p>
    
    <div class="text-center">
        <a href="{{ $url }}" class="btn">Voir ma demande</a>
    </div>
    
    @if($status === 'rejected')
        <p class="mt-4">Si vous avez des questions concernant le refus de votre demande, n'hésitez pas à contacter votre responsable.</p>
    @endif
    
    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>
@endsection
