<?php

use FumeApp\ModelTyper\Overrides\ModelInspector;
use Illuminate\Support\Facades\Config;

test('override can be resolved by application', function () {
    expect(resolve(ModelInspector::class))->toBeInstanceOf(ModelInspector::class);
});

test('override can set custom relationships', function () {
    // set custom_relationships in config
    Config::set('modeltyper.custom_relationships', [
        'hasCustomRelationship',
        'belongsToCustomRelationship',
    ]);

    $override = app(ModelInspector::class);

    $relationMethods = (new ReflectionClass($override))
        ->getProperty('relationMethods')
        ->getValue($override);

    expect($relationMethods)->toBeArray();
    expect(value: $relationMethods)->toContain(needle: 'hasCustomRelationship');
    expect(value: $relationMethods)->toContain(needle: 'belongsToCustomRelationship');
});
