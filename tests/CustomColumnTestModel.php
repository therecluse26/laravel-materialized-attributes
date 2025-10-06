<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

use Illuminate\Database\Eloquent\Model;
use TheRecluse26\MaterializedAttributes\Attributes\Materialized;
use TheRecluse26\MaterializedAttributes\Traits\Materializable;

class CustomColumnTestModel extends Model
{
    use Materializable;

    protected $table = 'test_models';

    protected $fillable = ['name', 'count'];

    protected function getMaterializableColumn(): string
    {
        return 'custom_column';
    }

    protected $casts = [
        'materialized' => 'array',
        'custom_column' => 'array',
    ];

    #[Materialized('summary')]
    public function getSummaryAttribute(): array
    {
        return [
            'name' => $this->name,
            'count' => $this->count,
            'computed_at' => 'fixed-timestamp',
        ];
    }
}
