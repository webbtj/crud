<?php

namespace Webbtj\Crud\Traits;

use Webbtj\Crud\Util;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

trait PublisherTrait
{
    use \Webbtj\Crud\Traits\CrudControllerTrait;

    protected $variables;
    protected $stdOut;
    protected $file;

    public function __construct()
    {
        parent::__construct();
        $this->variables = [];
        $this->stdOut = [
            'success' => [],
            'warning' => [],
            'error' => [],
            'routes' => ['web' => [], 'api' => []],
            'config' => [],
        ];
        $this->file = new Filesystem();
    }

    private function publishController(){
        $destinationFile = sprintf('%sController.php', $this->variables['MODEL']);
        $destinationPath = sprintf('%s/Http/Controllers', app_path());
        $sourceStub = dirname(dirname(__FILE__)) . '/stubs/WebController.php.stub';

        if($this->publishFile($sourceStub, $destinationPath, $destinationFile)){
            $this->stdOut['routes']['web'][] = sprintf("Route::resource('%s', '%sController');", $this->variables['ROUTES'], $this->variables['MODEL']);
        }
    }

    private function publishApiController(){
        $destinationFile = sprintf('%sController.php', $this->variables['MODEL']);
        $destinationPath = sprintf('%s/Http/Controllers/Api', app_path());
        $sourceStub = dirname(dirname(__FILE__)) . '/stubs/ApiController.php.stub';

        if($this->publishFile($sourceStub, $destinationPath, $destinationFile)){
            $this->stdOut['routes']['api'][] = sprintf("Route::resource('%s', 'Api\%sController');", $this->variables['ROUTES'], $this->variables['MODEL']);
        }
    }

    private function publishViews(){
        $this->populateProperties($this->variables['ROUTES']);

        $this->publishView('index', true);
        $this->publishView('show');
        $this->publishView('create');
        $this->publishView('edit');
        $this->publishLayout();
    }

    private function publishLayout($supressWarning = false){
        $sourceStub = dirname(dirname(__FILE__)) . '/stubs/views/layout.blade.php.stub';
        $destinationFile = 'crud-layout.blade.php';
        $destinationPath = resource_path('views');
        $this->publishFile($sourceStub, $destinationPath, $destinationFile, $supressWarning);
    }

    private function publishView($type, $hasHeaders = false){
        $columns = '';
        $columnSourceStub = dirname(dirname(__FILE__)) . "/stubs/views/partials/$type-column.blade.php.stub";
        $columnStub = $this->file->get($columnSourceStub);

        if($hasHeaders){
            $headers = '';
            $headerSourceStub = dirname(dirname(__FILE__)) . "/stubs/views/partials/$type-header.blade.php.stub";
            $headerStub = $this->file->get($headerSourceStub);
        }

        foreach($this->getColumns() as $field){
            if($this->fieldDisplayed($field, $type)){
                if($hasHeaders){
                    $headers .= str_replace('{FIELD}', $field->getDisplayName(), $headerStub);
                }

                $columns .= str_replace('{FIELD_VALUE}', $this->getDisplayVariable($field, $type), $columnStub);
            }
        }

        $this->variables[strtoupper($type) . '_COLUMNS'] = rtrim($columns);

        if($hasHeaders){
            $this->variables[strtoupper($type) . '_HEADERS'] = rtrim($headers);
        }

        $sourceStub = dirname(dirname(__FILE__)) . "/stubs/views/$type.blade.php.stub";
        $destinationFile = "$type.blade.php";
        $destinationPath = resource_path('views/' . $this->variables['VIEWS']);
        $this->publishFile($sourceStub, $destinationPath, $destinationFile);
    }

    private function publishFile($sourceStub, $destinationPath, $destinationFile, $supressWarning = false){
        $filePath = sprintf('%s/%s', $destinationPath, $destinationFile);

        if(!$this->file->isDirectory($destinationPath)){
            $this->file->makeDirectory($destinationPath, 0755, true);
        }

        if($this->file->exists($filePath)){
            if(!$supressWarning){
                $this->stdOut['error'][] = "$filePath already exists";
                return false;
            }
        }else{
            $stub = $this->file->get($sourceStub);
            foreach($this->variables as $variable => $value){
                $stub = str_replace(sprintf('{%s}', $variable), $value, $stub);
            }
            $this->file->put($filePath, $stub);
            $this->stdOut['success'][] = sprintf("%s created.", $filePath);
            return true;
        }
    }

    private function resolveModel($modelName){
        $models = Util::crud_config();

        collect($models)->each(function($model) use($modelName){
            $baseModelName = class_basename($modelName);
            $namespacePrependedModelName = app()->getNamespace() . $modelName;
            $validKeys = [
                strtolower($modelName), strtolower($baseModelName),
                strtolower($namespacePrependedModelName)
            ];

            if(in_array(strtolower($model['model']), $validKeys)){
                $this->variables = [
                    'APP_NAMESPACE' => app()->getNamespace(),
                    'MODEL_NAMESPACE' => $model['model'],
                    'MODEL' => class_basename($model['model']),
                    'MODEL_PLURAL' => Str::plural(class_basename($model['model'])),
                    'VIEWS' => strtolower(Str::plural($baseModelName)),
                    'ROUTES' => strtolower(Str::plural($baseModelName)),
                    'VARIABLE' => strtolower($baseModelName),
                    'VARIABLE_PLURAL' => strtolower(Str::plural($baseModelName)),
                    'UPDATE_EXCEPTIONS' => $this->var_export($model['update']['exclude']),
                    'STORE_EXCEPTIONS' => $this->var_export($model['store']['exclude']),
                    'UPDATE_VALIDATION' => $this->var_export($model['update']['validation']),
                    'STORE_VALIDATION' => $this->var_export($model['store']['validation']),
                ];
            }
        });
    }

    private function fieldDisplayed($field, $type){
        switch ($type) {
            case 'show':
            case 'edit':
                return $field->getViews()[$type] !== 'exclude';
                break;
            case 'create':
                return $field->getViews()[$type] !== 'exclude' && $field->getViews()[$type] !== 'readonly';
                break;
            case 'index':
                return $field->getViews()[$type] === 'include';
                break;
            default:
                return null;
        }
    }

    private function getDisplayVariable($field, $display="index"){
        $output = '';
        if($field->getViews()[$display] === 'readonly'){
            $sourceStub = dirname(dirname(__FILE__)) . sprintf('/stubs/views/partials/fields/%s/_readonly.blade.php.stub', $display, $field->getType());
        }else{
            $sourceStub = dirname(dirname(__FILE__)) . sprintf('/stubs/views/partials/fields/%s/%s.blade.php.stub', $display, $field->getType());
            if(!$this->file->isFile($sourceStub)){
                $sourceStub = dirname(dirname(__FILE__)) . sprintf('/stubs/views/partials/fields/%s/_general.blade.php.stub', $display);
            }
        }

        $stub = $this->file->get($sourceStub);

        if(!empty($field->getOptions()) && ($display === 'create' || $display === 'edit')){
            $field_options = '';
            $sourceOptionStub = dirname(dirname(__FILE__)) . sprintf('/stubs/views/partials/fields/%s/%s-option.blade.php.stub', $display, $field->getType());
            foreach($field->getOptions() as $option){
                $optionStub = $this->file->get($sourceOptionStub);
                $optionStub = str_replace('{FIELD_OPTION}', $option, $optionStub);
                $optionStub = str_replace('{FIELD_OPTION_MACHINE}', Str::kebab($option), $optionStub);
                $field_options .= $optionStub;
            }
            $stub = str_replace('{FIELD_OPTIONS}',  $field_options, $stub);
        }

        $stub = str_replace('{MODEL_VARIABLE}',  sprintf('$%s->%s', $this->variables['VARIABLE'], $field->getColumnName()), $stub);
        $stub = str_replace('{FIELD_DISPLAY_NAME}',  $field->getDisplayName(), $stub);
        $stub = str_replace('{FIELD_MACHINE_NAME}',  $field->getColumnName(), $stub);
        $stub = rtrim($stub);

        return $stub;
    }

    private function var_export($var){
        if(is_array($var) && empty($var)){
            $export = 'array()';
        }else{
            $export = var_export($var, true);
            $lines = explode("\n", $export);
            foreach($lines as &$line){
                $line = str_repeat(" ", 12) . $line;
            }
            $export = "\n" . implode("\n", $lines) . "\n" . str_repeat(" ", 8);
        }
        return $export;
    }
}
