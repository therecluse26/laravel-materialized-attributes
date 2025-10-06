<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

use Illuminate\Database\Eloquent\Model;
use TheRecluse26\MaterializedAttributes\Attributes\Materialized;
use TheRecluse26\MaterializedAttributes\Traits\Materializable;

class TestModel extends Model
{
    use Materializable;

    protected $table = 'test_models';

    protected $fillable = ['name', 'count'];

    protected $casts = [
        'materialized' => 'array',
        'custom_column' => 'array',
    ];

    public function getTitleAttribute(): string
    {
        return strtoupper($this->name);
    }

    #[Materialized('summary')]
    public function getSummaryAttribute(): array
    {
        return [
            'name' => $this->name,
            'count' => $this->count,
            'computed_at' => 'fixed-timestamp',
        ];
    }

    #[Materialized('metrics')]
    public function getMetricsAttribute(): array
    {
        return [
            'doubled_count' => $this->count * 2,
            'name_length' => strlen($this->name),
        ];
    }

    #[Materialized('flags')]
    public function getFlagsAttribute(): array
    {
        return [
            'has_count' => $this->count > 0,
            'long_name' => strlen($this->name) > 5,
        ];
    }
}
