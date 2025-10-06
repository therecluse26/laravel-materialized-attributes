<?php

namespace TheRecluse26\MaterializedAttributes;

use Illuminate\Support\ServiceProvider;
use TheRecluse26\MaterializedAttributes\Commands\AddMaterializedColumnCommand;
use TheRecluse26\MaterializedAttributes\Commands\RefreshMaterializedCommand;

class MaterializedAttributesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/materialized.php' => config_path('materialized.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/materialized.php', 'materialized');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddMaterializedColumnCommand::class,
                RefreshMaterializedCommand::class,
            ]);
        }
    }
}
