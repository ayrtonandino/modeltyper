<?php

use FumeApp\ModelTyper\Commands\ShowModelTyperMappingsCommand;
use FumeApp\ModelTyper\Constants\TypescriptMappings;

test('command can be executed successfully', function () {
    $this->artisan(ShowModelTyperMappingsCommand::class)->assertSuccessful();
});

test('command generates expected output', function () {
    TypescriptMappings::$mappings = [
        'a' => '1',
        'b' => '2',
    ];

    $this->artisan(ShowModelTyperMappingsCommand::class)
        ->expectsTable(
            headers: ['From PHP Type', 'To TypeScript Type'],
            rows: [
                ['a', '1'],
                ['b', '2'],
            ]
        )
        ->expectsOutputToContain('Showing type conversion table using timestamps-date set to false');
});
