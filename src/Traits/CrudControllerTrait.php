<?php

namespace Webbtj\Crud\Traits;

use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Webbtj\Crud\Util;
use Webbtj\Crud\Field;

trait CrudControllerTrait
{


    protected $model;
    protected $modelName;
    protected $routeRoot;

    public function __construct()
    {
        $this->populateProperties();
    }

    public function populateProperties($currentRouteName = null)
    {
        $_model = null;
        $_routeRoot = null;
        $_modelName = null;
        Util::crudModels()->each(
            function ($class, $resource) use (&$_model, &$_routeRoot, &$_modelName, $currentRouteName) {
                if (is_null($currentRouteName)) {
                    $currentRouteName = str_replace('api.', '', Route::currentRouteName());
                }
                $resourceCompare = preg_replace('/[^A-Za-z0-9]/', '', $resource);
                $currentRouteNameCompare = preg_replace('/[^A-Za-z0-9]/', '', $currentRouteName);
                if (Str::startsWith($currentRouteNameCompare, $resourceCompare)) {
                    $_model = new $class();
                    $_routeRoot = $resource;
                    $_modelName = class_basename($class);
                }
            }
        );
        $this->model = $_model;
        $this->routeRoot = $_routeRoot;
        $this->modelName = $_modelName;
    }

    public function getColumns()
    {
        $schema = DB::getDoctrineSchemaManager();
        $table = $schema->listTableDetails($this->model->getTable());
        $output = [];

        $fieldsShow = $fieldsEdit = $fieldsCreate = $fieldsIndex = [];

        $crudModels = Util::crudConfig();
        foreach ($crudModels as $crudModel) {
            if (is_array($crudModel) && isset($crudModel['model']) && $crudModel['model'] == get_class($this->model)) {
                if (isset($crudModel['show'])) {
                    $fieldsShow = $crudModel['show'];
                }
                if (isset($crudModel['edit'])) {
                    $fieldsEdit = $crudModel['edit'];
                }
                if (isset($crudModel['create'])) {
                    $fieldsCreate = $crudModel['create'];
                }
                if (isset($crudModel['index'])) {
                    $fieldsIndex = $crudModel['index'];
                }
            }
        }

        foreach ($table->getColumns() as $column) {
            $columnName = $column->getName();
            $className = get_class($column->getType());
            $type = array_key_exists($columnName, $this->model->getCasts())
                ? $this->model->getCasts()[$columnName]
                : null;

            $views = ['show' => null, 'edit' => null, 'create' => null, 'index' => null];
            foreach (['exclude', 'include', 'readonly'] as $verb) {
                if (isset($fieldsShow[$verb]) && in_array($columnName, $fieldsShow[$verb])) {
                    $views['show'] = $verb;
                }
                if (isset($fieldsEdit[$verb]) && in_array($columnName, $fieldsEdit[$verb])) {
                    $views['edit'] = $verb;
                }
                if (isset($fieldsCreate[$verb]) && in_array($columnName, $fieldsCreate[$verb])) {
                    $views['create'] = $verb;
                }
                if (isset($fieldsIndex[$verb]) && in_array($columnName, $fieldsIndex[$verb])) {
                    $views['index'] = $verb;
                }
            }

            $output[] = new Field($column->getName(), $className, $type, $this->model->getTable(), $views);
        }

        return $output;
    }

    public function view($template, $params = [])
    {
        $fields = $this->getColumns();
        $routeRoot = $this->routeRoot;
        $modelName = $this->modelName;

        $view = str_replace('crud::', '', $template);
        $crudModels = Util::crudConfig();
        foreach ($crudModels as $crudModel) {
            if (is_array($crudModel) && isset($crudModel['model']) && $crudModel['model'] == get_class($this->model)) {
                if (isset($crudModel[$view])) {
                    if (isset($crudModel[$view]['include']) &&
                        !empty($crudModel[$view]['include']) &&
                        is_array($crudModel[$view]['include'])
                    ) {
                        foreach ($fields as $k => $field) {
                            if (!in_array($field->getColumnName(), $crudModel[$view]['include'])) {
                                unset($fields[$k]);
                            }
                        }
                    } elseif (isset($crudModel[$view]['exclude']) &&
                        !empty($crudModel[$view]['exclude']) &&
                        is_array($crudModel[$view]['exclude'])
                    ) {
                        foreach ($fields as $k => $field) {
                            if (in_array($field->getColumnName(), $crudModel[$view]['exclude'])) {
                                unset($fields[$k]);
                            }
                        }
                    }
                    if (isset($crudModel[$view]['readonly']) &&
                        !empty($crudModel[$view]['readonly']) &&
                        is_array($crudModel[$view]['readonly'])
                    ) {
                        foreach ($fields as &$field) {
                            if (in_array($field->getColumnName(), $crudModel[$view]['readonly'])) {
                                $field->setReadOnly(true);
                            }
                        }
                    }
                }
            }
        }

        $params = array_merge($params, compact('fields', 'routeRoot', 'modelName'));

        $specific_template = str_replace('crud::', strtolower($modelName) . '.', $template);

        if (view()->exists($specific_template)) {
            return view($specific_template, $params);
        } else {
            return view($template, $params);
        }
    }

    public function customValidation(Request $request, $method)
    {
        $crudModel = Util::modelConfig(get_class($this->model));
        if ($crudModel) {
            if (isset($crudModel[$method]) && isset($crudModel[$method]['validation'])) {
                $request->validate($crudModel[$method]['validation']);
            }
        }
    }

    public function validInputs(Request $request, $method)
    {
        $this->customValidation($request, $method);
        $crudModel = Util::modelConfig(get_class($this->model));

        $inputs = $request->except($crudModel[$method]['exclude']);

        $columns = $this->getColumns();
        foreach ($inputs as $k => $v) {
            $inputs[$k] = Util::sanitizeFieldValue($k, $v, $columns);
        }
        return $inputs;
    }
}
