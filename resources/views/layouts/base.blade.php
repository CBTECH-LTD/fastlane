<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Page Title --}}
    <title>Control Panel | {{ config('app.name') }}</title>

    {{-- Styles --}}
    <link href="{{ asset('/vendor/fastlane/fonts/inter.css') }}" rel="stylesheet"/>
    <link href="{{ asset('/vendor/fastlane/icons/css/line-awesome.min.css') }}" rel="stylesheet"/>
    <link href="{{ mix('app.css', 'vendor/fastlane') }}" rel="stylesheet"/>

    @livewireStyles
    @stack('styles')

    {{-- Main Script --}}
    <script src="{{ mix('app.js', 'vendor/fastlane') }}" defer></script>

    {{-- Livewire scripts --}}
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbo-eval="false"></script>
</head>
<body class="font-sans antialiased h-screen {{ $bg ?? 'bg-gray-200' }}">

{{ $slot }}

@stack('modals')

<script>
    window.fastlane = {
        locale: '{{ config('app.locale', 'en') }}',
        translations: @json(\CbtechLtd\Fastlane\Fastlane::getTranslations())
    }
</script>

@stack('scripts')
</body>
</html>
