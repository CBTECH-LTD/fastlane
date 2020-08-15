<?php

use CbtechLtd\Fastlane\FastlaneFacade;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Fastlane API',
    ]);
});

Route::middleware(['fastlane.auth:fastlane-api'])->group(function (\Illuminate\Routing\Router $router) {
    // Register Entry Types routes
    FastlaneFacade::registerApiRoutes($router);
});
