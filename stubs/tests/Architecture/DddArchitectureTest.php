<?php

arch('domain does not depend on outer application layers')
    ->skip(
        fn () => glob(dirname(__DIR__, 3).'/app/Domain', GLOB_ONLYDIR) === [],
        'Skipping, Domain folder doesn\'t exist.',
    )
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
    ->skip(
        fn () => glob(dirname(__DIR__, 3).'/app/Application', GLOB_ONLYDIR) === [],
        'Skipping, Application folder doesn\'t exist.',
    )
    ->expect('App\Application')
    ->not->toUse('App\Infrastructure');

arch('application does not depend on delivery mechanisms')
    ->skip(
        fn () => glob(dirname(__DIR__, 3).'/app/Application', GLOB_ONLYDIR) === [],
        'Skipping, Application folder doesn\'t exist.',
    )
    ->expect('App\Application')
    ->not->toUse([
        'App\Http',
        'App\Console',
        'App\Jobs',
        'App\Listeners',
    ]);

arch('actions expose handle')
    ->skip(
        fn () => glob(dirname(__DIR__, 3).'/app/Application/*/Actions', GLOB_ONLYDIR) === [],
        'Skipping, Application\Actions folders doesn\'t exist.',
    )
    ->expect('App\Application\*\Actions')
    ->classes()
    ->toHaveMethod('handle');

arch('application data uses Data suffix')
    ->skip(
        fn () => glob(dirname(__DIR__, 3).'/app/Application/*/Data', GLOB_ONLYDIR) === [],
        'Skipping, Application\Data doesn\'t exist.',
    )
    ->expect('App\Application\*\Data')
    ->classes()
    ->toHaveSuffix('Data');
