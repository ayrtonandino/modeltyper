<?php

use App\Models\User;
use FumeApp\ModelTyper\Actions\WriteColumnAttribute;

beforeEach(function () {
    $this->attribute = [
        'name' => 'role_traditional',
        'type' => null,
        'increments' => false,
        'nullable' => null,
        'default' => null,
        'unique' => null,
        'fillable' => true,
        'hidden' => false,
        'appended' => false,
        'cast' => 'accessor',
    ];

    $this->mappings = [
        'array' => 'string[]',
        'bigint' => 'number',
        'bool' => 'boolean',
        'boolean' => 'boolean',
    ];
});

test('action can be resolved by application', function () {
    expect(resolve(WriteColumnAttribute::class))->toBeInstanceOf(WriteColumnAttribute::class);
});

test('action can be executed', function () {
    $action = app(WriteColumnAttribute::class);
    $result = $action(new ReflectionClass(User::class), $this->attribute, $this->mappings);

    expect($result)->toBeArray();

    expect($result)->toHaveKey('0');
    expect($result)->toHaveKey('1');
});
