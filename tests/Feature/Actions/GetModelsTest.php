<?php

use App\Models\User;
use FumeApp\ModelTyper\Actions\GetModels;

test('action can be resolved by application', function () {
    expect(resolve(GetModels::class))->toBeInstanceOf(GetModels::class);
});

test('action returns only one file when model is specified', function () {
    $action = app(GetModels::class);
    expect($action('User'))->toHaveCount(1);
});

test('action accepts fully qualified classname as model', function () {
    $action = app(GetModels::class);
    expect($action(User::class))->toHaveCount(1);
});

test('action can find all models in project', function () {
    $action = app(GetModels::class);

    $foundModels = $action();

    expect($foundModels)->toHaveCount(4);
    $this->assertStringContainsString('Complex.php', $foundModels[0]->getRelativePathname());
    $this->assertStringContainsString('Pivot.php', $foundModels[1]->getRelativePathname());
    $this->assertStringContainsString('User.php', $foundModels[2]->getRelativePathname());
    $this->assertStringContainsString('Team.php', $foundModels[3]->getRelativePathname());
});
