<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Fastlane API',
    ]);
});

Route::middleware(['fastlane.auth:fastlane-api'])->group(function (\Illuminate\Routing\Router $router) {
    // TODO: Register Entry Types routes
});
