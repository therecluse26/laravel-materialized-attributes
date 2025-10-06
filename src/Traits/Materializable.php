<?php

namespace TheRecluse26\MaterializedAttributes\Traits;

use Illuminate\Support\Str;
use ReflectionMethod;
use TheRecluse26\MaterializedAttributes\Attributes\Materialized;

trait Materializable
{
    protected array $_materializedCache_ = [];

    protected function getMaterializableColumn(): string
    {
        return 'materialized';
    }

    public function getAttribute($key)
    {
        $method = 'get'.Str::studly($key).'Attribute';

        if (! method_exists($this, $method)) {
            return parent::getAttribute($key);
        }

        $ref = new ReflectionMethod($this, $method);
        $attr = $ref->getAttributes(Materialized::class)[0] ?? null;

        if (! $attr) {
            return parent::getAttribute($key);
        }

        $meta = $attr->newInstance();
        $column = $this->getMaterializableColumn();

        $payload = $this->materializedPayload($column);

        if (array_key_exists($meta->key, $payload)) {
            return $payload[$meta->key];
        }

        $value = $this->$method();
        $payload[$meta->key] = $value;

        $this->_materializedCache_ = $payload;
        $this->setAttribute($column, $payload);
        $this->save();

        return $value;
    }

    public function invalidateMaterialized(string $key): void
    {
        $column = $this->getMaterializableColumn();
        $payload = $this->materializedPayload($column);

        if (array_key_exists($key, $payload)) {
            unset($payload[$key]);
            $this->_materializedCache_ = $payload;
            $this->setAttribute($column, empty($payload) ? null : $payload);
            $this->save();
        }
    }

    public function invalidateAllMaterialized(): void
    {
        $column = $this->getMaterializableColumn();
        $this->_materializedCache_ = [];
        $this->setAttribute($column, null);
        $this->save();
    }

    public function refresh()
    {
        $result = parent::refresh();
        $this->_materializedCache_ = [];

        return $result;
    }

    protected function materializedPayload(string $column): array
    {
        if (! empty($this->_materializedCache_)) {
            return $this->_materializedCache_;
        }

        $raw = $this->getAttributeValue($column);

        if (is_array($raw)) {
            return $this->_materializedCache_ = $raw;
        }

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            return $this->_materializedCache_ = is_array($decoded) ? $decoded : [];
        }

        return $this->_materializedCache_ = [];
    }
}
