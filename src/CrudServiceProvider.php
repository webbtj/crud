<?php

namespace Webbtj\Crud;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use DB;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Str;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            'Webbtj\Crud\Commands\Publisher'
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // include helpers;
        require_once(__DIR__.'/utilities/helpers.php');

        // support for enums
        $schema = DB::getDoctrineSchemaManager();
        Type::addType('enum', 'Webbtj\Crud\EnumType');
        $schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'enum');

        // publish configs
        $this->publishes([
            __DIR__.'/config/crud.php' => config_path('crud.php'),
        ], 'crud-config');

        // default views
        $this->loadViewsFrom(__DIR__.'/views/crud', 'crud');

        // publish views
        $this->publishes([
            __DIR__.'/views/crud' => resource_path('views/vendor/webbtj/crud'),
        ], 'crud-views');

        // register crud model routes
        collect(Util::crudConfig())->each(function ($model) {
            $classBasename = class_basename($model['model']);
            $resource = Str::kebab(Str::plural($classBasename), '-');
            $middleware = array_merge(['web'], $model['middleware']['web'] ?? []);
            Route::resource($resource, 'Webbtj\Crud\Http\Controllers\CrudWebController')->middleware($middleware);

            Route::prefix('api')->name('api.')->group(function () use ($model, $resource) {
                $middleware = array_merge(['api'], $model['middleware']['api'] ?? []);
                Route::resource($resource, 'Webbtj\Crud\Http\Controllers\CrudApiController')
                    ->except(['create', 'edit'])
                    ->middleware($middleware);
            });
        });
    }
}
