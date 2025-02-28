@component('mail::message')
# Mise à jour de votre demande de congé

Bonjour {{ $notifiable->name }},

@if($conge->statut === 'accepte')
Votre demande de congé a été **acceptée**.
@elseif($conge->statut === 'refuse')
Votre demande de congé a été **refusée**.
@else
Le statut de votre demande de congé a été mis à jour.
@endif

**Détails de la demande :**
* **Période :** du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
* **Statut :** {{ $conge->statut }}

@component('mail::button', ['url' => url('/conges')])
Voir mes congés
@endcomponent

Pour toute question, veuillez contacter votre responsable.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent 