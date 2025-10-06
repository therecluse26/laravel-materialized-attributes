<?php

namespace TheRecluse26\MaterializedAttributes\Commands;

use Illuminate\Console\Command;

class RefreshMaterializedCommand extends Command
{
    protected $signature = 'materialize:refresh {--model=*}';

    protected $description = 'Recompute and persist all annotated materialized attributes for selected models (or all).';

    public function handle(): int
    {
        $this->info('Materialized attributes refresh scaffold executed.');

        return self::SUCCESS;
    }
}
