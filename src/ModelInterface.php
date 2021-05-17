<?php


namespace fumeapp\ModelTyper;


use Exception;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ModelInterface
{

    public array $mappings = [
        'bigint' => 'number',
        'int' => 'number',
        'integer' => 'number',
        'text' => 'string',
        'string' => 'string',
        'datetime' => 'Date',
        'bool' => 'boolean',
        'boolean' => 'boolean',
    ];

    public function generate(): string
    {
        $allCode = '';
        $models = $this->getModels();
        foreach ($models as $model) {
            $interface = $this->getInterface(new $model());
            $allCode .= $this->getCode($interface);
        }
        return $allCode;
    }

    /**
     * @throws ReflectionException
     */
    public function getInterface(Model $model): TypescriptInterface
    {
        $columns = $this->getColumns($model);
        $mutators = $this->getMutators($model);
        $relations = $this->getRelations($model);
        return new TypescriptInterface(
            name: (new ReflectionClass($model))->getShortName(),
            columns: $columns,
            mutators: $mutators,
            relations: $relations,
        );
    }

    public function getCode(TypescriptInterface $interface): string
    {
        $code = "export interface {$interface->name} {\r\n";
        if (count($interface->columns) > 0) {
            $code .= "  // columns\r\n";
            foreach ($interface->columns as $key => $value) {
                $code .= "  {$key}: {$value}\r\n";
            }
        }
        if (count($interface->mutators) > 0) {
            $code .= "  // mutators\r\n";
            foreach ($interface->mutators as $key => $value) {
                $code .= "  {$key}: {$value}\r\n";
            }
        }
        if (count($interface->relations) > 0) {
            $code .= "  // relations\r\n";
            foreach ($interface->relations as $key => $value) {
                $code .= "  {$key}: {$value}\r\n";
            }
        }
        $code .= "} \r\n";
        $plural = Str::plural($interface->name);
        $code .= "export type $plural = Array<{$interface->name}> \r\n\r\n";
        return $code;
    }


    /**
     * @throws ReflectionException
     */
    public function getRelations(Model $model): array
    {
        $relations = [];
        $methods = get_class_methods($model);
        foreach ($methods as $method) {
            $reflection = new ReflectionMethod($model, $method);
            if ($reflection->hasReturnType()) {
                $type = (string) $reflection->getReturnType();
                if ($type === 'Illuminate\Database\Eloquent\Relations\BelongsTo' ||
                    $type === 'Illuminate\Database\Eloquent\Relations\HasOne'
                ) {
                    $code = file($reflection->getFileName())[$reflection->getEndLine()-2];
                    preg_match('/\((.*?)::class/', $code, $matches);
                    if ($matches[1]) {
                        $relations[$method] = $matches[1];
                    }
                }
                if ($type === 'Illuminate\Database\Eloquent\Relations\BelongsToMany' ||
                    $type === 'Illuminate\Database\Eloquent\Relations\HasMany'
                ) {
                    $code = file($reflection->getFileName())[$reflection->getEndLine()-2];
                    preg_match('/\((.*?)::class/', $code, $matches);
                    if ($matches[1]) {
                        $relations[$method] = $matches[1] . 's';
                    }
                }
            }
        }
        return $relations;
    }

    /**
     * @throws ReflectionException
     */
    public function getMutators(Model $model): array
    {
        $mutations = [];
        $mutators = $model->getMutatedAttributes();
        foreach ($mutators as $mutator) {
            $method = 'get' . $this->camelize($mutator) . 'Attribute';
            $reflection = new ReflectionMethod($model, $method);
            if (!$reflection->hasReturnType()) {
                throw new Exception(
                    "Model for table {$model->getTable()} has no return type for mutator: {$mutator}"
                );
            }
            $mutations[$mutator] = $this->mapReturnType((string) $reflection->getReturnType());
        }
        return $mutations;
    }

    private function mapReturnType($returnType): string
    {
        if ($returnType[0] === '?') {
            return $this->mappings[str_replace('?', '', $returnType)]  . ' | null';
        }
        if (!isset($this->mappings[$returnType])) {
            return $returnType;
        }
        return $this->mappings[$returnType];
    }

    public function getColumns(Model $model): array
    {
        $columns = [];
        foreach ($this->getColumnList($model) as $columnName) {
            $column = $this->getColumn($model, $columnName);
            if (!isset($this->mappings[$column->getType()->getName()])) {
                ray($column->getType()->getName());
            } else {
                $columns[$columnName] = $this->mappings[ $column->getType()->getName() ];
            }
        }
        return $columns;
    }

    public function getColumnList(Model $model): array
    {
        return $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
    }

    public function getColumn(Model $model, string $column): Column
    {
        return $model->getConnection()->getDoctrineColumn($model->getTable(), $column);
    }

    /**
     * Get a list of all models
     * @return Collection
     */
    public function getModels(): Collection
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                return sprintf(
                    '\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );
            })->filter(function ($class) {
                $valid = false;
                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
                }
                return $valid;
            });
        return $models->values();
    }

    /**
     * under_scores to CamelCase
     * @param $input
     * @param string $separator
     * @return string
     */
    private function camelize($input, $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

}