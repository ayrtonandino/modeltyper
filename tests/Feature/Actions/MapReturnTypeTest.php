<?php

use FumeApp\ModelTyper\Actions\MapReturnType;

test('action can be resolved by application', function () {
    expect(resolve(MapReturnType::class))->toBeInstanceOf(MapReturnType::class);
});

test('action can be executed', function () {
    $action = app(MapReturnType::class);
    expect($action('A', []))->toBeString();
});

test('action can return correct type', function () {
    $action = app(MapReturnType::class);
    expect($action('B', ['a' => '1', 'b' => '2']))->toEqual('2');
});

test('action can return correct nullable type', function () {
    $action = app(MapReturnType::class);
    expect($action('?A', ['a' => '1', 'b' => '2']))->toEqual('1 | null');
});

test('action can return unknown type', function () {
    $action = app(MapReturnType::class);
    expect($action('C', ['a' => '1', 'b' => '2']))->toEqual('unknown');
});

test('action throws exception empty string', function () {
    $this->expectException(ErrorException::class);
    $this->expectExceptionMessage('Empty string');

    $action = app(MapReturnType::class);
    expect($action('', []))->toBeString();
});
