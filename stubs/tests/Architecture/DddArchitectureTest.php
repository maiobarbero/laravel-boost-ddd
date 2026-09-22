<?php

arch('domain does not depend on outer application layers')
    ->skip(
        fn () => glob(dirname(__DIR__, 2).'/app/Domain', GLOB_ONLYDIR) === [],
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
        'App\Policies',
        'App\Providers',
    ]);

arch('application does not depend on infrastructure')
    ->skip(
        fn () => glob(dirname(__DIR__, 2).'/app/Application', GLOB_ONLYDIR) === [],
        'Skipping, Application folder doesn\'t exist.',
    )
    ->expect('App\Application')
    ->not->toUse('App\Infrastructure');

arch('application does not depend on delivery mechanisms')
    ->skip(
        fn () => glob(dirname(__DIR__, 2).'/app/Application', GLOB_ONLYDIR) === [],
        'Skipping, Application folder doesn\'t exist.',
    )
    ->expect('App\Application')
    ->not->toUse([
        'App\Http',
        'App\Console',
        'App\Jobs',
        'App\Listeners',
        'App\Mail',
        'App\Notifications',
        'App\Policies',
        'App\Providers',
    ]);

foreach (['Domain', 'Application'] as $layer) {
    arch(strtolower($layer).' does not depend on transport input or output')
        ->skip(
            fn () => ! is_dir(dirname(__DIR__, 2).'/app/'.$layer),
            'Skipping, '.$layer.' folder does not exist.',
        )
        ->expect('App\\'.$layer)
        ->not->toUse([
            'Illuminate\Http',
            'Illuminate\Foundation\Http',
            'Illuminate\Console',
            'Illuminate\Support\Facades\Http',
            'Illuminate\Support\Facades\Request',
            'Illuminate\Support\Facades\Response',
            'Illuminate\Support\Facades\Route',
            'request',
            'response',
            'abort',
            'abort_if',
            'abort_unless',
        ]);
}

arch('actions expose handle')
    ->skip(
        fn () => glob(dirname(__DIR__, 2).'/app/Application/*/Actions', GLOB_ONLYDIR) === [],
        'Skipping, Application\Actions folders doesn\'t exist.',
    )
    ->expect('App\Application\*\Actions')
    ->classes()
    ->toHaveMethod('handle');

arch('application data uses Data suffix')
    ->skip(
        fn () => glob(dirname(__DIR__, 2).'/app/Application/*/Data', GLOB_ONLYDIR) === [],
        'Skipping, Application\Data doesn\'t exist.',
    )
    ->expect('App\Application\*\Data')
    ->classes()
    ->toHaveSuffix('Data');
