<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use TheRecluse26\MaterializedAttributes\MaterializedAttributesServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'TheRecluse26\\MaterializedAttributes\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }


    protected function getPackageProviders($app)
    {
        return [
            MaterializedAttributesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/database/migrations/create_test_models_table.php';
        $migration->up();
    }
}
