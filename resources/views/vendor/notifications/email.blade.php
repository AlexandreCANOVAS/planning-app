<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Oups !')
@else
# @lang('Bonjour !')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<div style="text-align: center; margin: 30px 0;">
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
</div>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
<div style="margin-top: 15px;">
@lang('Cordialement,')<br>
<strong>{{ config('app.name') }}</strong>
</div>
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
<div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 0.9em; color: #6b7280;">
@lang(
    "Si vous ne parvenez pas à cliquer sur le bouton \":actionText\", copiez et collez l'URL ci-dessous\n".'
    dans votre navigateur web :',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</div>
</x-slot:subcopy>
@endisset
</x-mail::message>
