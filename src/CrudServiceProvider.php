<?php

namespace Webbtj\Crud;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Webbtj\Crud\Util;
use DB;
use Doctrine\DBAL\Types\Type;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
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
        Util::crud_models()->each(function($model, $resource){
            Route::resource($resource, 'Webbtj\Crud\CrudController')->middleware('web');
        });
    }
}
