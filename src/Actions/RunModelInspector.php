<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelInspector;

class RunModelInspector
{
    public function __construct(protected ?Application $app = null)
    {
        $this->app = $app ?? app();
    }

    /**
     * Run internal Laravel ModelInspector class.
     *
     * @see https://github.com/laravel/framework/blob/11.x/src/Illuminate\Database\Eloquent\ModelInspector.php
     *
     * @return array{"class": class-string<\Illuminate\Database\Eloquent\Model>, database: string, table: string, policy: class-string|null, attributes: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, events: \Illuminate\Support\Collection, observers: \Illuminate\Support\Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<\Illuminate\Database\Eloquent\Model>>, builder: class-string<\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>}|null
     */
    public function __invoke(string $model, bool $resolveAbstract = false): ?array
    {
        return app(ModelInspector::class)->inspect($model);
    }
}