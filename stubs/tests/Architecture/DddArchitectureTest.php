<?php

arch('domain does not depend on outer application layers')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Application',
        'App\Infrastructure',
        'App\Http',
        'App\Console',
        'App\Jobs',
        'App\Listeners',
        'App\Mail',
        'App\Notifications',
    ]);

arch('application does not depend on infrastructure')
    ->expect('App\Application')
    ->not->toUse('App\Infrastructure');

arch('application does not depend on delivery mechanisms')
    ->expect('App\Application')
    ->not->toUse([
        'App\Http',
        'App\Console',
        'App\Jobs',
        'App\Listeners',
    ]);

arch('actions expose handle')
    ->expect('App\Application\*\Actions')
    ->classes()
    ->toHaveMethod('handle');

arch('application data uses Data suffix')
    ->expect('App\Application\*\Data')
    ->classes()
    ->toHaveSuffix('Data');
