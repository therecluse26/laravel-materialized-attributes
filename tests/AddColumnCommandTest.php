<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

use Illuminate\Support\Facades\File;

class AddColumnCommandTest extends TestCase
{
    /** @test */
    public function creates_migration_with_default_column_name()
    {
        $this->artisan('materialize:add-column', ['table' => 'users'])
            ->assertExitCode(0);

        $files = File::glob(database_path('migrations/*_add_materialized_column_to_users_table.php'));
        $this->assertCount(1, $files);

        $content = File::get($files[0]);
        $this->assertStringContainsString('Schema::table(\'users\'', $content);
        $this->assertStringContainsString('$table->json(\'materialized\')->nullable();', $content);
        $this->assertStringContainsString('$table->dropColumn(\'materialized\');', $content);

        File::delete($files[0]);
    }

    /** @test */
    public function creates_migration_with_custom_column_name()
    {
        $this->artisan('materialize:add-column', [
            'table' => 'posts',
            '--column' => 'computed_data',
        ])->assertExitCode(0);

        $files = File::glob(database_path('migrations/*_add_computed_data_column_to_posts_table.php'));
        $this->assertCount(1, $files);

        $content = File::get($files[0]);
        $this->assertStringContainsString('Schema::table(\'posts\'', $content);
        $this->assertStringContainsString('$table->json(\'computed_data\')->nullable();', $content);
        $this->assertStringContainsString('$table->dropColumn(\'computed_data\');', $content);

        File::delete($files[0]);
    }
}
