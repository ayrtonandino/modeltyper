<?php

use App\Models\AbstractModel;
use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;

test('action can be resolved by application', function () {
    expect(resolve(Generator::class))->toBeInstanceOf(Generator::class);
});

test('action can be executed', function () {
    $action = app(Generator::class);
    $result = $action();

    expect($result)->toBeString();
});

test('action throws exception on non existent class', function () {
    $this->expectException(ModelTyperException::class);
    $this->expectExceptionMessage('No models found.');

    $action = app(Generator::class);
    $action('nonExistentClass');
});

test('action throws exception on abstract model', function () {
    $this->expectException(ModelTyperException::class);
    $this->expectExceptionMessage('No models found.');

    $action = app(Generator::class);
    $action(AbstractModel::class);
});
