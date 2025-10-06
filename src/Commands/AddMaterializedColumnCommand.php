<?php

namespace TheRecluse26\MaterializedAttributes\Commands;

use Illuminate\Console\Command;

class AddMaterializedColumnCommand extends Command
{
    protected $signature = 'materialize:add-column {table} {--column=materialized}';

    protected $description = 'Create a migration to add a JSON column for materialized attributes to the given table.';

    public function handle(): int
    {
        $table = $this->argument('table');
        $column = $this->option('column');

        $file = now()->format('Y_m_d_His').'_add_'.$column.'_column_to_'.$table.'_table.php';
        $path = database_path('migrations/'.$file);

        $stub = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            \$table->json('{$column}')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            \$table->dropColumn('{$column}');
        });
    }
};
PHP;

        file_put_contents($path, $stub);
        $this->info("Migration created: {$path}");

        return self::SUCCESS;
    }
}
