<?php

use App\Models\User;
use FumeApp\ModelTyper\Actions\BuildModelDetails;
use FumeApp\ModelTyper\Actions\GetModels;

test('action can be resolved by application', function () {
    expect(resolve(BuildModelDetails::class))->toBeInstanceOf(BuildModelDetails::class);
});

test('action can be executed', function () {
    $models = app(GetModels::class)(User::class);
    $action = app(BuildModelDetails::class);

    $result = $action($models->first());

    expect($result)->toBeArray();

    expect($result)->toHaveKey('reflectionModel');
    expect($result)->toHaveKey('name');
    expect($result)->toHaveKey('columns');
    expect($result)->toHaveKey('nonColumns');
    expect($result)->toHaveKey('relations');
    expect($result)->toHaveKey('interfaces');
    expect($result)->toHaveKey('imports');
});
