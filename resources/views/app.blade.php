<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    
    <link href="{{ asset('/vendor/fastlane/fonts/inter.css') }}" rel="stylesheet"/>
    <link href="{{ asset('/vendor/fastlane/icons/css/line-awesome.min.css') }}" rel="stylesheet"/>
    <link href="{{ mix('app.css', 'vendor/fastlane') }}" rel="stylesheet"/>
</head>
<body class="font-sans antialiased h-screen bg-gray-100">
@inertia

<script src="{{ mix('manifest.js', 'vendor/fastlane') }}"></script>
<script src="{{ mix('vendor.js', 'vendor/fastlane') }}"></script>
<script src="{{ mix('app.js', 'vendor/fastlane') }}"></script>
</body>
</html>
