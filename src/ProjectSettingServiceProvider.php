<?php

namespace Mabrouk\ProjectSetting;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mabrouk\ProjectSetting\Console\Commands\ProjectSettingInstallCommand;

class ProjectSettingServiceProvider extends ServiceProvider
{
    private $packageMigrations = [
        'create_project_setting_groups_table',
        'create_project_setting_group_translations_table',
        'create_project_setting_sections_table',
        'create_project_setting_section_translations_table',
        'create_project_settings_table',
        'create_project_setting_translations_table',
        'create_project_setting_types_table',
    ];

    private $packageSeeders = [
        'ProjectSettingGroupsTableSeeder',
        'ProjectSettingSectionsWithSettingItemsSeeder',
    ];

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
        require_once __DIR__ . '/Helpers/ProjectSettingHelperFunctions.php';

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {

            $this->commands([
                ProjectSettingInstallCommand::class,
                // ProjectSettingTypeUpdateCommand::class,
            ]);

            /**
             * Migrations
             */
            $migrationFiles = $this->migrationFiles();
            if (\count($migrationFiles) > 0) {
                $this->publishes($migrationFiles, 'project_setting_migrations');
            }

            /**
             * Seeders
             */
            $seedersFiles = $this->seedersFiles();
            if (\count($seedersFiles) > 0) {
                $this->publishes($seedersFiles, 'project_setting_seeders');
            }

            /**
             * Config and static translations
             */
            $this->publishes([
                __DIR__ . '/config/project_settings.php' => config_path('project_settings.php'), // ? Config
                __DIR__ . '/resources/lang' => App::langPath(), // ? Static translations
            ]);
        }
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/project_settings_admin_routes.php');
            $this->loadRoutesFrom(__DIR__ . '/routes/project_settings_client_routes.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'namespace' => 'Mabrouk\ProjectSetting\Http\Controllers',
            'prefix' => config('project_settings.package_routes_prefix'),
        ];
    }

    protected function migrationFiles()
    {
        $migrationFiles = [];

        foreach ($this->packageMigrations as $migrationName) {
            if (! $this->migrationExists($migrationName)) {
                $migrationFiles[__DIR__ . "/database/migrations/{$migrationName}.php.stub"] = database_path('migrations/' . date('Y_m_d_His', time()) . "_{$migrationName}.php");
            }
        }
        return $migrationFiles;
    }

    protected function seedersFiles()
    {
        $seedersFiles = [];

        foreach ($this->packageSeeders as $seederName) {
            if (! $this->seederExists($seederName)) {
                $seedersFiles[__DIR__ . "/database/seeders/{$seederName}.php.stub"] = database_path("seeders/{$seederName}.php");
            }
        }
        return $seedersFiles;
    }

    protected function migrationExists($migrationName)
    {
        $path = database_path('migrations/');
        $files = \scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = \strpos($value, $migrationName);
            if ($pos !== false) return true;
        }
        return false;
    }

    protected function seederExists($seederName)
    {
        $path = database_path('seeders/');
        $files = \scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = \strpos($value, $seederName);
            if ($pos !== false) return true;
        }
        return false;
    }
}
