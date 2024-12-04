<?php

namespace Tests\Traits;

use ReflectionClass;

trait ResolveClassAsReflection
{
    /**
     * Resolve a class as a ReflectionClass.
     *
     * @template TModel of class-string<\Illuminate\Database\Eloquent\Model>
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return \ReflectionClass<\Illuminate\Database\Eloquent\Model>
     */
    private function resolveClassAsReflection(string $model): ReflectionClass
    {
        return new ReflectionClass($model);
    }
}
