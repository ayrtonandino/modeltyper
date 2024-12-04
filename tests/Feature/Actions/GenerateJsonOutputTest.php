<?php

use FumeApp\ModelTyper\Actions\GenerateJsonOutput;
use FumeApp\ModelTyper\Actions\GetMappings;
use FumeApp\ModelTyper\Actions\GetModels;

test('action can be resolved by application', function () {
    expect(resolve(GenerateJsonOutput::class))->toBeInstanceOf(GenerateJsonOutput::class);
});

test('action can be executed', function () {
    $action = app(GenerateJsonOutput::class);
    $result = $action(app(GetModels::class)(), app(GetMappings::class)());

    expect($result)->toBeString();
});
