@component('fastlane::app')

    <div class="h-screen flex">
        <div class="flex items-center justify-center w-full md:w-1/2 bg-white px-8 py-12">
            <div class="max-w-md w-full p-8 rounded">
                <div class="flex flex-col justify-center items-center">
                    <img src="{{ asset(config('fastlane.asset_logo_img')) }}" alt="" class="w-20 mb-8">
                    <h2 class="text-center text-3xl leading-9 font-extrabold text-gray-900">
                        {{ __('fastlane::core.login.title') }}
                    </h2>
                </div>

                <form method="POST" action="{{ route('fastlane.cp.login') }}" class="mt-8">
                    @csrf

                    @if ($errors->isNotEmpty())
                        <div class="w-full mb-4 rounded border border-red-200 p-4 bg-red-100 text-red-500">
                            @foreach($errors as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    <x-fl-field :errors="$errors->get('email')">
                        <x-slot name="label">{{ __('fastlane::core.login.email') }}</x-slot>
                        <input type="email" name="email" required autofocus class="form-input w-full" placeholder="{{ __('fastlane::core.login.email') }}">
                    </x-fl-field>
                    <x-fl-field :errors="$errors->get('password')">
                        <x-slot name="label">{{ __('fastlane::core.login.password') }}</x-slot>
                        <input type="password" name="password" required class="form-input w-full" placeholder="{{ __('fastlane::core.login.password') }}">
                    </x-fl-field>
                    <div class="py-2 text-right text-sm leading-5">
                        <x-fl-link href="#" color="gray">
                            {{ __('fastlane::core.login.forgot') }}
                        </x-fl-link>
                    </div>
                    <div class="mt-2">
                        <x-fl-button submit full size="lg" color="black">{{ __('fastlane::core.login.button') }}</x-fl-button>
                    </div>
                </form>
            </div>
        </div>
        <div class="hidden md:block flex-grow h-full bg-brand-900 right-panel" @if (config('fastlane.asset_login_bg')) style="background-image: url('{{ asset(config('fastlane.asset_login_bg')) }}')" @endif></div>
    </div>

    @push('styles')
        <style>
            .right-panel {
                -webkit-box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
                -moz-box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
                box-shadow: 0px 0px 120px 12px rgba(0, 0, 0, 0.30);
                background-size: cover;
                background-repeat: no-repeat;
            }
        </style>
    @endpush

@endcomponent
