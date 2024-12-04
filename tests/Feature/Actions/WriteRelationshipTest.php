<?php

use FumeApp\ModelTyper\Actions\WriteRelationship;

beforeEach(function () {
    $this->relation = [
        'name' => 'notifications',
        'type' => 'MorphMany',
        'related' => "Illuminate\Notifications\DatabaseNotification",
    ];
});

test('action can be resolved by application', function () {
    expect(resolve(WriteRelationship::class))->toBeInstanceOf(WriteRelationship::class);
});

test('action can be executed', function () {
    $action = app(WriteRelationship::class);
    $result = $action($this->relation);

    expect($result)->toBeString();

    $this->assertStringContainsString('notifications: DatabaseNotification[]', $result);
});

test('action can return array', function () {
    $action = app(WriteRelationship::class);
    $result = $action(relation: $this->relation, jsonOutput: true);

    expect($result)->toBeArray();

    expect($result)->toEqual(['name' => 'notifications', 'type' => 'DatabaseNotification[]']);
});

test('action can be indented', function () {
    $action = app(WriteRelationship::class);
    $result = $action(relation: $this->relation, indent: 'ASDF');

    $this->assertStringContainsString('ASDF  notifications: DatabaseNotification[]', $result);
});

test('action can return optional relationships', function () {
    $action = app(WriteRelationship::class);
    $result = $action(relation: $this->relation, optionalRelation: true);

    $this->assertStringContainsString('notifications?: DatabaseNotification[]', $result);
});

test('action can return optional relationships as array', function () {
    $action = app(WriteRelationship::class);
    $result = $action(relation: $this->relation, optionalRelation: true, jsonOutput: true);

    expect($result)->toEqual(['name' => 'notifications?', 'type' => 'DatabaseNotification[]']);
});

test('action can return plural relationships', function () {
    $action = app(WriteRelationship::class);
    $result = $action(relation: $this->relation, plurals: true);

    $this->assertStringContainsString('notifications: DatabaseNotifications', $result);
});
