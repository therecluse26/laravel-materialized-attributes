<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

class RefreshCommandTest extends TestCase
{
    /** @test */
    public function refresh_command_executes_successfully()
    {
        $this->artisan('materialize:refresh')
            ->expectsOutput('Materialized attributes refresh scaffold executed.')
            ->assertExitCode(0);
    }

    /** @test */
    public function refresh_command_accepts_model_option()
    {
        $this->artisan('materialize:refresh', ['--model' => ['User', 'Post']])
            ->expectsOutput('Materialized attributes refresh scaffold executed.')
            ->assertExitCode(0);
    }
}
