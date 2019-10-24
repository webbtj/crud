<?php

namespace Webbtj\Crud;

use Illuminate\Support\Str;
use DB;

class Util
{
    public static function crudModels()
    {
        return collect(Util::crudConfig())->mapWithKeys(function ($class) {
            if (is_array($class) && isset($class['model'])) {
                $class = $class['model'];
            }
            $classBasename = class_basename($class);
            $resource = Str::kebab(Str::plural($classBasename), '-');

            return [$resource => $class];
        });
    }

    public static function crudConfig()
    {
        $models = config('crud.models');
        $protectedColumns = ['id', 'created_at', 'updated_at'];
        $nonCrudProperties = ['_token',  '_method'];
        if ($models) {
            foreach ($models as $modelKey => $model) {
                if (is_string($model)) {
                    $model = [
                        'model' => $model,
                        'show' => [],
                        'edit' => [],
                        'create' => [],
                        'index' => [],
                        'update' => [],
                        'store' => []
                    ];
                }
                foreach (['show', 'edit', 'create', 'index'] as $method) {
                    if (!isset($model[$method])) {
                        $model[$method] = [];
                    }
                    foreach (['include', 'exclude', 'readonly'] as $verb) {
                        if (!isset($model[$method][$verb])) {
                            $model[$method][$verb] = [];
                        }
                    }
                }
                foreach (['update', 'store'] as $method) {
                    if (!isset($model[$method])) {
                        $model[$method] = [];
                    }
                    foreach (['validation', 'exclude'] as $verb) {
                        if (!isset($model[$method][$verb])) {
                            $model[$method][$verb] = [];
                        }
                    }
                }

                $model['edit']['readonly'] = array_merge($model['edit']['readonly'] ?? [], $protectedColumns);

                foreach ($protectedColumns as $protectedColumn) {
                    if (($k = array_search($protectedColumn, $model['create']['include'])) !== false) {
                        unset($model['create']['include'][$k]);
                    } else {
                        $model['create']['exclude'] = array_merge($model['create']['exclude'], [$protectedColumn]);
                    }
                }

                $model['store']['exclude'] = array_merge(
                    $model['store']['exclude'],
                    $protectedColumns,
                    $nonCrudProperties
                );
                $model['update']['exclude'] = array_merge(
                    $model['update']['exclude'],
                    $protectedColumns,
                    $nonCrudProperties
                );
                $models[$modelKey] = $model;
            }
        }

        return $models;
    }

    public static function modelConfig($className)
    {
        $models = Util::crudConfig();
        foreach ($models as $model) {
            if ($model['model'] == $className) {
                return $model;
            }
        }
        return null;
    }

    public static function enumOptions(String $table, String $field)
    {
        $type = DB::select(DB::raw(sprintf('SHOW COLUMNS FROM %s WHERE Field = "%s"', $table, $field)))[0]->Type;
        preg_match('/^(enum|set)\((.*)\)$/', $type, $matches);
        $values = [];
        if ($matches) {
            foreach (explode(',', $matches[2]) as $value) {
                $values[] = trim($value, "'");
            }
        }
        return $values;
    }

    public static function sanitizeFieldValue($key, $value, $fields)
    {
        foreach ($fields as $field) {
            if ($key == $field->getColumnName()) {
                switch ($field->getType()) {
                    case 'array':
                        if (is_array($value)) {
                            $value = implode(",", $value);
                        }
                        break;
                }
            }
        }
        return $value;
    }
}
