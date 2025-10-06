<?php

namespace TheRecluse26\MaterializedAttributes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Materialized
{
    public function __construct(public string $key) {}
}
