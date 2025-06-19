@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'bg-red-50 border-l-4 border-red-500 p-4 rounded-md my-3']) }}>
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <ul class="text-sm text-red-700 font-medium">
                    @foreach ((array) $messages as $message)
                        @if ($message == 'These credentials do not match our records.')
                            <li>Les identifiants saisis ne correspondent à aucun compte. Veuillez vérifier votre email et mot de passe.</li>
                        @elseif ($message == 'The provided password is incorrect.')
                            <li>Le mot de passe saisi est incorrect. Veuillez réessayer.</li>
                        @elseif ($message == 'The password field is required.')
                            <li>Le champ mot de passe est obligatoire.</li>
                        @elseif ($message == 'The email field is required.')
                            <li>Le champ email est obligatoire.</li>
                        @elseif ($message == 'The email field must be a valid email address.')
                            <li>Le format de l'adresse email est invalide.</li>
                        @else
                            <li>{{ $message }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
