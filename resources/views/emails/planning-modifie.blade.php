@component('mail::message')
# Modification de votre planning

Bonjour {{ $notifiable->name }},

Votre planning a été modifié pour la date du {{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}.

**Détails du planning :**
* **Horaires :** {{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}
* **Lieu :** {{ $planning->lieu->nom }}
* **Durée :** {{ number_format($planning->heures_travaillees, 2) }} heures

@component('mail::button', ['url' => url('/dashboard')])
Voir mon planning
@endcomponent

Si vous avez des questions, n'hésitez pas à contacter votre responsable.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent 