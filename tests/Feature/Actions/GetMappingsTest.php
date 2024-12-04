<?php

use FumeApp\ModelTyper\Actions\GetMappings;
use Illuminate\Support\Facades\Config;

test('action can be resolved by application', function () {
    expect(resolve(GetMappings::class))->toBeInstanceOf(GetMappings::class);
});

test('action can set timestamps as date', function () {
    $action = app(GetMappings::class);

    $mappings = $action(setTimestampsToDate: true);

    expect($mappings)->toHaveKey('date');
    expect($mappings)->toHaveKey('immutable_date');
    expect($mappings)->toHaveKey('datetime');
    expect($mappings)->toHaveKey('immutable_datetime');
    expect($mappings)->toHaveKey('immutable_custom_datetime');
    expect($mappings)->toHaveKey('timestamp');

    expect($mappings['date'])->toEqual('Date');
    expect($mappings['immutable_date'])->toEqual('Date');
    expect($mappings['datetime'])->toEqual('Date');
    expect($mappings['immutable_datetime'])->toEqual('Date');
    expect($mappings['immutable_custom_datetime'])->toEqual('Date');
    expect($mappings['timestamp'])->toEqual('Date');
});

test('action can merge user config', function () {
    Config::set('modeltyper.custom_mappings', [
        'userDefinedConfig' => 'SomeType',
    ]);

    $action = app(GetMappings::class);

    $mappings = $action();

    expect($mappings)->toHaveKey('userdefinedconfig');
    expect($mappings['userdefinedconfig'])->toEqual('SomeType');
});

test('action can use user config to override default mappings', function () {
    $action = app(GetMappings::class);

    $mappings = $action();

    expect($mappings)->toHaveKey('text');
    expect($mappings['text'])->toEqual('string');

    Config::set('modeltyper.custom_mappings', [
        'text' => 'number',
    ]);

    $mappings = $action();

    expect($mappings['text'])->toEqual('number');
});
