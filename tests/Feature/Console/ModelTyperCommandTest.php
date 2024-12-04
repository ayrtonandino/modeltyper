<?php

use App\Models\Complex;
use App\Models\User;
use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Illuminate\Support\Facades\Config;

uses(\Tests\Traits\GeneratesOutput::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(\Tests\Traits\UsesInputFiles::class);

afterEach(function () {
    $this->deleteOutput();
});

test('command can be executed successfully', function () {
    $this->artisan(ModelTyperCommand::class)->assertSuccessful();
});

test('command generates expected output for user model', function () {
    $expected = $this->getExpectedContent('user.ts');
    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
    ])->expectsOutput($expected);
});

test('command generates expected output for user model when output file argument is set', function () {
    $expected = $this->getExpectedContent('user.ts');

    $this->artisan(ModelTyperCommand::class, [
        'output-file' => './tests/output/models.d.ts',
        '--model' => User::class,
    ])
        ->expectsOutput('Typescript interfaces generated in ./tests/output/models.d.ts file');

    $actual = $this->getGeneratedFileContents('models.d.ts');

    expect($actual)->toBe($expected);
});

test('command generates fillables when option is enabled', function () {
    $expected = $this->getExpectedContent('user-fillables.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--fillables' => true,
        '--fillable-suffix' => 'Editable',
    ])->expectsOutput($expected);
});

test('command generates global when option is enabled', function () {
    // set global-namespace config
    Config::set('modeltyper.global-namespace', 'App.Models');

    $expected = $this->getExpectedContent('user-global.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--global' => true,
    ])->expectsOutput($expected);
});

test('command generates json when option is enabled', function () {
    if (PHP_OS_FAMILY === 'Windows') {
        $this->markTestSkipped('Fails on windows because of /r/n characters');
    }

    $expected = $this->getExpectedContent('user.json', true);

    $this->artisan(ModelTyperCommand::class, [
        'output-file' => './tests/output/user.json',
        '--model' => User::class,
        '--json' => true,
        '--use-enums' => true,
    ])->expectsOutput('Typescript interfaces generated in ./tests/output/user.json file');

    $actual = $this->getGeneratedFileContents('user.json');

    expect($actual)->toBe($expected);
});

test('command generates use enums when option is enabled', function () {
    $expected = $this->getExpectedContent('user-enums.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--use-enums' => true,
    ])->expectsOutput($expected);
});

test('command generates plurals when option is enabled', function () {
    $expected = $this->getExpectedContent('user-plurals.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--plurals' => true,
    ])->expectsOutput($expected);
});

test('command generates no relations when option is enabled', function () {
    $expected = $this->getExpectedContent('user-no-relations.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--no-relations' => true,
    ])->expectsOutput($expected);
});

test('command generates optional relations when option is enabled', function () {
    $expected = $this->getExpectedContent('user-optional-relations.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--optional-relations' => true,
    ])->expectsOutput($expected);
});

test('command generates no hidden when option is enabled', function () {
    $expected = $this->getExpectedContent('user-no-hidden.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--no-hidden' => true,
    ])->expectsOutput($expected);
});

test('command generates timestamps date when option is enabled', function () {
    $expected = $this->getExpectedContent('user-timestamps-date.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--timestamps-date' => true,
    ])->expectsOutput($expected);
});

test('command generates optional nullables when option is enabled', function () {
    $expected = $this->getExpectedContent('user-optional-nullables.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--optional-nullables' => true,
    ])->expectsOutput($expected);
});

test('command generates api resources when option is enabled', function () {
    $expected = $this->getExpectedContent('user-api-resource.ts');

    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--api-resources' => true,
    ])->expectsOutput($expected);
});

test('command can ignore config when option is enabled', function () {
    Config::set('modeltyper.global', true);
    Config::set('modeltyper.fillables', false);
    Config::set('modeltyper.fillable-suffix', 'FillableSuffix');

    $expected = $this->getExpectedContent('user-fillables.ts');
    $this->artisan(ModelTyperCommand::class, [
        '--model' => User::class,
        '--fillables' => true,
        '--fillable-suffix' => 'Editable',
        '--ignore-config' => true,
    ])->expectsOutput($expected);
});

test('command generates expected output for complex model', function () {
    $expected = $this->getExpectedContent('complex-model.ts');
    $this->artisan(ModelTyperCommand::class, [
        '--model' => Complex::class,
    ])->expectsOutput($expected);
});

test('command generates expected output for complex model when user types unknown custom cast', function () {
    // set UpperCast return type in config
    Config::set('modeltyper.custom_mappings', [
        'App\Casts\UpperCast' => 'string',
    ]);

    $expected = $this->getExpectedContent('complex-model-with-cast.ts');
    $this->artisan(ModelTyperCommand::class, [
        '--model' => Complex::class,
    ])->expectsOutput($expected);
});
