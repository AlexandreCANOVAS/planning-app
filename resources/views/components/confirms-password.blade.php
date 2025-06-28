@props([
    'title' => __('Confirmer le mot de passe'),
    'content' => __('Pour votre sécurité, veuillez confirmer votre mot de passe pour continuer.'),
    'button' => __('Confirmer'),
    'promptOnMount' => false,
])

<div
    x-data="{ show: @js($promptOnMount), confirmed: false }"
    x-on:confirming-password.window="setTimeout(() => $refs.confirmable.focus(), 250)"
    x-on:confirmed-password.window="confirmed = true"
>
    <div x-on:click="show = !show" x-show="!show && !confirmed" {{ $attributes }}>
        {{ $slot }}
    </div>

    <div
        x-cloak
        x-show="show && !confirmed"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        x-ref="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="show && !confirmed"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                x-on:click="show = false"
                aria-hidden="true"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="show && !confirmed"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom bg-white rounded-lg shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
            >
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            {{ $title }}
                        </h3>

                        <div class="mt-2">
                            <p class="text-sm text-gray-600">
                                {{ $content }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <div
                                x-data="{ confirmablePassword: '', error: '', loading: false }"
                                x-on:confirmed-password.window="
                                    setTimeout(() => $el.closest('.sm\\:max-w-lg').remove(), 250);
                                    @this.{{ $attributes->get('wire:then') }}()
                                "
                            >
                                <input
                                    type="password"
                                    class="block w-3/4 mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    placeholder="{{ __('Mot de passe') }}"
                                    x-ref="confirmable"
                                    wire:model.defer="confirmablePassword"
                                    x-model="confirmablePassword"
                                    x-on:keydown.enter="confirm"
                                />

                                <div class="mt-2 text-sm text-red-600" x-text="error" x-show="error"></div>

                                <div class="flex justify-end mt-4">
                                    <button
                                        type="button"
                                        class="inline-flex justify-center px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        x-on:click="show = false"
                                    >
                                        {{ __('Annuler') }}
                                    </button>

                                    <button
                                        type="button"
                                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        x-bind:class="{ 'opacity-25': loading }"
                                        x-on:click="confirm"
                                        x-bind:disabled="loading"
                                    >
                                        {{ $button }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirm() {
        this.loading = true;
        
        Livewire.dispatch('confirmPassword', {
            password: this.confirmablePassword
        }).then(result => {
            if (result.error) {
                this.error = result.error;
                this.loading = false;
            } else {
                this.confirmed = true;
                this.loading = false;
                window.dispatchEvent(new CustomEvent('confirmed-password'));
            }
        });
    }
</script>
