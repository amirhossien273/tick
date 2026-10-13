<?php

return [
    App\Providers\AppServiceProvider::class,
    ...(
    env('APP_ENV', 'production') === 'local'
        ? [App\Providers\TelescopeServiceProvider::class]
        : []
    ),
];
