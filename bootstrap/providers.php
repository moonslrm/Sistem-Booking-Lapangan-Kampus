<?php

use App\Providers\AppServiceProvider;
use App\Providers\HorizonServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;

return [
    AppServiceProvider::class,
    HorizonServiceProvider::class,
    SanctumServiceProvider::class,
    PermissionServiceProvider::class,
    ActivitylogServiceProvider::class,
];
