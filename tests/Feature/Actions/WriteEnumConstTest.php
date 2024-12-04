<?php

use App\Enums\Roles;
use FumeApp\ModelTyper\Actions\WriteEnumConst;

uses(\Tests\Traits\GeneratesOutput::class);
uses(\Tests\Traits\ResolveClassAsReflection::class);
uses(\Tests\Traits\UsesInputFiles::class);

test('action can be resolved by application', function () {
    expect(resolve(WriteEnumConst::class))->toBeInstanceOf(WriteEnumConst::class);
});

test('action can be executed and returns string', function () {
    $action = app(WriteEnumConst::class);
    $reflectionModel = $this->resolveClassAsReflection(Roles::class);

    $result = $action($reflectionModel);

    $expected = $this->getExpectedContent('enum.ts', true);

    expect($result)->toBeString();
    expect($result)->toEqual($expected);
});

test('action can be executed and returns array', function () {
    $action = app(WriteEnumConst::class);
    $reflectionModel = $this->resolveClassAsReflection(Roles::class);

    $result = $action(reflection: $reflectionModel, jsonOutput: true);

    expect($result)->toBeArray();
    expect($result)->toHaveKey('name');
    expect($result)->toHaveKey('type');
    expect($result['name'])->toEqual('Roles');
    expect($result['type'])->toBeString();
});
