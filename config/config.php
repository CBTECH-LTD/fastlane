<?php

return [
    /**
     * Assets used by the Fastlane control panel.
     * You can customize these values to have a proper
     * logo and login background image for your project.
     *
     * ATTENTION: The value must be a relative path to the
     * public directory.
     */
    'asset_logo_img' => 'vendor/fastlane/img/app-logo.png',
    'asset_login_bg' => 'vendor/fastlane/img/login-bg-black.jpg',

    /**
     * Configure the Entry Types which must be enabled
     * in Fastlane.
     */
    'entry_types'    => [
        //
    ],

    /**
     * Control panel settings.
     */
    'control_panel'  => [
        'middleware'          => ['web'],
        'url_prefix'          => env('FASTLANE_CP_URL_PREFIX', '/cp'),
        'pagination_per_page' => 20,
    ],

    /**
     * Content API configuration.
     */

    'api'               => [
        'enabled'    => env('FASTLANE_API_ENABLED', true),
        'url_prefix' => env('FASTLANE_API_URL_PREFIX', '/cp/api'),
        'middleware' => ['api'],
    ],

    /**
     * Class responsible for building the control panel menu.
     */
    'menu'              => \CbtechLtd\Fastlane\Support\Menu\MenuBuilder::class,

    /**
     * Minimum length of models Hash IDs.
     */
    'hashid_min_length' => 5,

    /*
     * Determine whether the Super Admin role is enabled in the system.
     */
    'super_admin'       => true,

    /**
     * Determine handlers and storage disk to be used for file attachments.
     */
    'attachments'       => [
        'draft_handler'     => CbtechLtd\Fastlane\FileAttachment\DraftAttachmentHandler::class,
        'permanent_handler' => '',
        'disk'              => 'public',
        'max_size'          => 10000000, // in kb
    ],
];
