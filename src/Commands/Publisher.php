<?php

namespace Webbtj\Crud\Commands;

use Illuminate\Console\Command;
use Webbtj\Crud\Util;
use Illuminate\Support\Str;

class Publisher extends Command
{
    use \Webbtj\Crud\Traits\PublisherTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:publish
                            {--type=* : The type of resource to publish, leave blank for all}
                            {--model=* : The models to publish, leave blank for all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish views and controllers for Crud\'d models';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $models = $this->option('model');
        if (!$models) {
            $models = collect(Util::crudConfig())->pluck('model')->toArray();
        }
        $types = $this->option('type');
        if (!$types) {
            $types = ['controller', 'api.controller', 'views'];
        }

        foreach ($models as $model) {
            $baseModelName = class_basename($model);
            $routes = strtolower(Str::plural($baseModelName));
            $this->populateProperties($routes);

            if (!$this->option('type')) {
                $this->stdOut['config'][] = sprintf(
                    "You should remove the config entries for %s in config/crud.php to avoid collisions.",
                    $model
                );
            }

            $this->resolveModel($model);
            foreach ($types as $type) {
                $type = Str::singular(strtolower(preg_replace('/[^A-Za-z0-9]/', '', $type)));
                switch ($type) {
                    case 'controller':
                        $this->publishController();
                        break;
                    case 'apicontroller':
                        $this->publishApiController();
                        break;
                    case 'view':
                        $this->publishViews();
                        break;
                    case 'indexview':
                    case 'viewindex':
                        $this->populateProperties($this->variables['ROUTES']);
                        $this->publishView('index', true);
                        $this->publishLayout(true);
                        break;
                    case 'showview':
                    case 'viewshow':
                        $this->populateProperties($this->variables['ROUTES']);
                        $this->publishView('show');
                        $this->publishLayout(true);
                        break;
                    case 'editview':
                    case 'viewedit':
                        $this->populateProperties($this->variables['ROUTES']);
                        $this->publishView('edit');
                        $this->publishLayout(true);
                        break;
                    case 'createview':
                    case 'viewcreate':
                        $this->populateProperties($this->variables['ROUTES']);
                        $this->publishView('create');
                        $this->publishLayout(true);
                        break;
                }
            }
        }
        $success = "\e[0;32;40m";
        $warning = "\e[0;33;40m";
        $error = "\e[1;37;41m";
        $routes = "\e[1;36;40m";
        $config = "\e[0;35;40m";
        $code = "\e[0;30;47m";
        $closing = "\e[0m";
        foreach (['success', 'warning', 'error'] as $messageType) {
            if (!empty($this->stdOut[$messageType])) {
                echo sprintf("\n%s:\n\n", strtoupper(Str::plural($messageType)));
                foreach ($this->stdOut[$messageType] as $string) {
                    echo sprintf("%s%s%s\n", $$messageType, $string, $closing);
                }
                echo "\n\n";
            }
        }
        if (!empty($this->stdOut['routes']['web']) || !empty($this->stdOut['routes']['api'])) {
            echo "\nTODO:\n\n";
        }

        if (!empty($this->stdOut['config'])) {
            foreach ($this->stdOut['config'] as $string) {
                echo sprintf("%s%s%s\n", $config, $string, $closing);
            }
        }

        foreach ($this->stdOut['routes'] as $routeType => $resources) {
            if (!empty($resources)) {
                echo sprintf(
                    "\n%s%s been created. You should add the following to your routes/%s.php if you haven't already.",
                    $routes,
                    count($resources) > 1 ? "New $routeType controllers have" : "A new $routeType controller has",
                    $routeType
                );
                echo "$code\n";
                foreach ($resources as $resource) {
                    echo "\n\t$resource";
                }
                echo "\n$closing\n";
            }
        }
    }
}
