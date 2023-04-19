<?php

namespace Crankd\RapidRevisions;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Crankd\RapidRevisions\Commands\RapidCrudCommand;
use Crankd\RapidRevisions\View\Components\CodeBlock;
use Crankd\RapidRevisions\Services\DirectiveThemeService;

class RapidRevisionsProvider extends ServiceProvider
{

    private const CONFIG_FILE = __DIR__ . '/../config/rapid-revisions.php';

    private const PATH_VIEWS = __DIR__ . '/../resources/views';

    private const PATH_ASSETS = __DIR__ . '/../resources';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->offerPublishing(); // Publish the config file

        $this->loadViewsFrom(self::PATH_VIEWS, 'rapid-revisions'); // Load the views

        $this->registerComponents(); // Register the components

        $this->registerDirectives(); // Register the directives

        $this->registerCommands(); // Register the commands

        $this->publishes([
            self::PATH_ASSETS . '/css' => resource_path('crankd/rapid-revisions/css/'), // Publish the assets
        ], 'rapid-revisions-css');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, 'rapid-revisions');
    }

    protected function offerPublishing()
    {
        if (!function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        // Migrations 
        $this->publishes([
            __DIR__ . '/../database/migrations/create_revisionables.php.stub' => $this->getMigrationFileName('create_revisionables.php'),
        ], 'rapid-custom-fields-migrations');


        $this->publishes([
            self::CONFIG_FILE => config_path('rapid-revisions.php'),
        ], 'rapid-revisions-config');
    }

    /**
     * Register the Blade form components.
     *
     * @return $this
     */
    private function registerComponents(): self
    {
        return $this;
    }

    private function registerDirectives(): self
    {
        return $this;
    }

    private function registerCommands(): self
    {

        return $this;
    }


    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path . '*_' . $migrationFileName);
            })
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
