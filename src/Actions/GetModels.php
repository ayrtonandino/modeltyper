<?php

namespace FumeApp\ModelTyper\Actions;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class GetModels
{
    /**
     * Return collection of models.
     *
     * @param  list<string>|null  $includedModels
     * @param  list<string>|null  $excludedModels
     * @param  list<string>|null  $additionalPaths
     * @return Collection<int, SplFileInfo>
     */
    public function __invoke(?string $model = null, ?array $includedModels = null, ?array $excludedModels = null, ?array $additionalPaths = null): Collection
    {
        $modelShortName = $this->resolveModelFilename($model);

        if (filled($includedModels)) {
            $includedModels = array_map(fn (string $includedModel): string => $this->resolveModelFilename($includedModel), $includedModels);
        }

        if (filled($excludedModels)) {
            $excludedModels = array_map(fn (string $excludedModel): string => $this->resolveModelFilename($excludedModel), $excludedModels);
        }

        return collect([app_path()])
            ->when(
                $additionalPaths,
                fn (Collection $colection, array $paths): Collection => $colection->merge($paths)
            )
            ->map(fn (string $path): array => ClassMapGenerator::createMap($path))
            ->collapseWithKeys()
            ->flip()
            ->filter(fn (string $class): bool => class_exists($class) && (new ReflectionClass($class))->isSubclassOf(EloquentModel::class))
            ->map(fn (string $fqn): string|false => $this->resolveModelFilename($fqn))
            ->when(
                $includedModels,
                fn (Collection $files, array $includedModels): Collection => $files
                    ->filter(fn (string $class): bool => in_array($class, $includedModels))
            )
            ->when(
                $excludedModels,
                fn (Collection $files, array $excludedModels): Collection => $files
                    ->reject(fn (string $class): bool => in_array($class, $excludedModels))
            )
            ->when(
                $modelShortName,
                fn (Collection $files): Collection => $files
                    ->filter(fn (string $class): bool => $class === $modelShortName)
            )
            ->keys()
            ->sort()
            ->map(fn (string $class): SplFileInfo => new SplFileInfo($class))
            ->values();
    }

    private function resolveModelFilename(?string $model): string|false
    {
        if ($model === null) {
            return false;
        }

        return Str::contains($model, '\\') ? Str::afterLast($model, '\\') : $model;
    }
}
