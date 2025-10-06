<?php

namespace TheRecluse26\MaterializedAttributes;

use Illuminate\Support\ServiceProvider;
use TheRecluse26\MaterializedAttributes\Commands\AddMaterializedColumnCommand;
use TheRecluse26\MaterializedAttributes\Commands\RefreshMaterializedCommand;

class MaterializedAttributesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AddMaterializedColumnCommand::class,
                RefreshMaterializedCommand::class,
            ]);
        }
    }
}
