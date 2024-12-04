<?php

use FumeApp\ModelTyper\Actions\GenerateCliOutput;
use FumeApp\ModelTyper\Actions\GetMappings;
use FumeApp\ModelTyper\Actions\GetModels;

test('action can be resolved by application', function () {
    expect(resolve(GenerateCliOutput::class))->toBeInstanceOf(GenerateCliOutput::class);
});

test('action can be executed', function () {
    $action = app(GenerateCliOutput::class);
    $result = $action(app(GetModels::class)(), app(GetMappings::class)());

    expect($result)->toBeString();
});
