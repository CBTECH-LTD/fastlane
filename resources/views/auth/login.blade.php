@component('fastlane::layouts.base', ['bg' => 'bg-gray-200'])
    <div class="container mx-auto h-screen flex items-center">
        <div class="hidden md:flex w-4/6 items-center">
            <div class="flex-shrink">
                <img src="{{ asset(config('fastlane.asset_logo_img')) }}" alt="" class="w-24 mr-8">
            </div>
            <div class="text-left">
                <h1 class="text-5xl text-brand-500 font-extrabold tracking-wider">{{ config('app.name') }}</h1>
                <h2 class="text-2xl leading-9 font-semibold text-gray-700">
                    {{ __('fastlane::core.login.title') }}
                </h2>
            </div>
        </div>
        <div class="w-full md:w-2/6">
            <div class="flex items-center justify-center bg-white p-6 shadow-lg rounded-lg">
                <div class="w-full">
                    <form method="POST" action="{{ route('fastlane.cp.login') }}" data-turbo="false">
                        @csrf

                        @if ($errors->has('email'))
                            <div class="w-full mb-4 rounded border border-red-200 p-4 bg-red-100 text-red-500">
                                {{ $errors->first('email') }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <input type="email" name="email" required autofocus class="form-input w-full p-4" placeholder="{{ __('fastlane::core.login.email') }}">
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" required class="form-input w-full p-4" placeholder="{{ __('fastlane::core.login.password') }}">
                        </div>

                        <div class="my-4">
                            <x-fl-button submit full size="xl" color="brand">{{ __('fastlane::core.login.button') }}</x-fl-button>
                        </div>

                        <div class="pt-4 text-center text-base leading-5">
                            <x-fl-link href="#" color="gray">
                                {{ __('fastlane::core.login.forgot') }}
                            </x-fl-link>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-sm text-gray-600 mt-4">
                <a href="{{ url('/') }}" class="no-underline hover:underline">{{ \Illuminate\Support\Str::replaceFirst('https://', '', \Illuminate\Support\Str::replaceFirst('http://', '', url('/'))) }}</a>
            </div>
        </div>

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
