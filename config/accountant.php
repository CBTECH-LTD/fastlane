<?php

declare(strict_types = 1);

return [

    /**
     * We just merge our accountant settings so user can customize it
     * by just publishing original Accountant configuration file.
     */
    'events' => [
        'created',
        'updated',
        'restored',
        'deleted',
        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ],

];
