<?php

describe('Pest presets', function () {

    // arch()->preset()->php();

    arch()->preset()->security();
});

describe('test Commands folder', function () {
    arch()->expect('FumeApp\ModelTyper\Commands')->toBeClasses();

    arch()->expect('FumeApp\ModelTyper\Commands')
        ->toHaveSuffix('Command');

    arch()->expect('FumeApp\ModelTyper\Commands')
        ->toHaveMethod('handle');

    arch()->expect('FumeApp\ModelTyper\Commands')->toOnlyBeUsedIn([
        'FumeApp\ModelTyper\ModelTyperServiceProvider',
        'FumeApp\ModelTyper\Listeners\RunModelTyperCommand',
    ]);

    arch()->expect('FumeApp\ModelTyper\Commands')->toExtend('Illuminate\Console\Command');
});
