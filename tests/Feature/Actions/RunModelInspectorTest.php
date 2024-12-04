<?php

use App\Models\AbstractModel;
use App\Models\User;
use FumeApp\ModelTyper\Actions\RunModelInspector;

test('action can be resolved by application', function () {
    expect(resolve(RunModelInspector::class))->toBeInstanceOf(RunModelInspector::class);
});

test('action can be executed', function () {
    $result = app(RunModelInspector::class)(User::class);

    expect($result)->toBeArray();

    expect($result)->toHaveKey('class');
    expect($result)->toHaveKey('database');
    expect($result)->toHaveKey('table');
    expect($result)->toHaveKey('policy');
    expect($result)->toHaveKey('attributes');
    expect($result)->toHaveKey('relations');
    expect($result)->toHaveKey('events');
    expect($result)->toHaveKey('observers');
    expect($result)->toHaveKey('collection');
    expect($result)->toHaveKey('builder');
});

test('action returns null on abstract model', function () {
    $result = app(RunModelInspector::class)(AbstractModel::class);

    expect($result)->toBeNull();
});

test('action returns null on non existing model', function () {
    $result = app(RunModelInspector::class)('NoExistsModel');

    expect($result)->toBeNull();
});
