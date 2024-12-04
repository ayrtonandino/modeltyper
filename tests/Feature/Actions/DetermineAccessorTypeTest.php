<?php

use App\Models\User;
use FumeApp\ModelTyper\Actions\DetermineAccessorType;

uses(\Tests\Traits\ResolveClassAsReflection::class);

test('action can be resolved by application', function () {
    expect(resolve(DetermineAccessorType::class))->toBeInstanceOf(DetermineAccessorType::class);
});

test('action can be executed', function () {
    $action = app(DetermineAccessorType::class);
    $result = $action(new ReflectionClass(User::class), 'roleNew');

    expect($result instanceof ReflectionMethod)->toBeTrue();
});

test('action can be executed for traditional accessor', function () {
    $action = app(DetermineAccessorType::class);
    $result = $action(new ReflectionClass(User::class), 'getRoleTraditionalAttribute');

    expect($result instanceof ReflectionMethod)->toBeTrue();
});

test('action throws exception on non existent accessor', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Accessor method for NonExistentAccessor on model ' . User::class . ' does not exist');

    $action = app(DetermineAccessorType::class);
    $action(new ReflectionClass(User::class), 'nonExistentAccessor');
});
