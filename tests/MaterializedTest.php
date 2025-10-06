<?php

namespace TheRecluse26\MaterializedAttributes\Tests;

class MaterializedTest extends TestCase
{
    /** @test */
    public function unannotated_accessors_behave_normally()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 1]);

        $this->assertEquals('TEST', $model->title);
        $this->assertEquals('TEST', $model->title);

        $this->assertNull($model->materialized);
    }

    /** @test */
    public function annotated_accessors_compute_persist_and_reuse()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 5]);

        $summary1 = $model->summary;
        $this->assertEquals('test', $summary1['name']);
        $this->assertEquals(5, $summary1['count']);
        $this->assertArrayHasKey('computed_at', $summary1);

        $model->refresh();
        $this->assertNotNull($model->materialized);
        $this->assertArrayHasKey('summary', $model->materialized);

        $summary2 = $model->summary;
        $this->assertEquals($summary1, $summary2);
    }

    /** @test */
    public function multiple_annotated_accessors_coexist()
    {
        $model = TestModel::create(['name' => 'testing', 'count' => 3]);

        $summary = $model->summary;
        $metrics = $model->metrics;
        $flags = $model->flags;

        $model->refresh();
        $materialized = $model->materialized;

        $this->assertArrayHasKey('summary', $materialized);
        $this->assertArrayHasKey('metrics', $materialized);
        $this->assertArrayHasKey('flags', $materialized);

        $this->assertEquals($summary, $materialized['summary']);
        $this->assertEquals($metrics, $materialized['metrics']);
        $this->assertEquals($flags, $materialized['flags']);
    }

    /** @test */
    public function can_invalidate_single_materialized_attribute()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 2]);

        $model->summary;
        $model->metrics;

        $model->refresh();
        $this->assertArrayHasKey('summary', $model->materialized);
        $this->assertArrayHasKey('metrics', $model->materialized);

        $model->invalidateMaterialized('summary');

        $model->refresh();
        $this->assertArrayNotHasKey('summary', $model->materialized);
        $this->assertArrayHasKey('metrics', $model->materialized);
    }

    /** @test */
    public function can_invalidate_all_materialized_attributes()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 2]);

        $model->summary;
        $model->metrics;
        $model->flags;

        $model->refresh();
        $this->assertNotEmpty($model->materialized);

        $model->invalidateAllMaterialized();

        $model->refresh();
        $this->assertNull($model->materialized);
    }

    /** @test */
    public function per_model_column_override_works()
    {
        $model = CustomColumnTestModel::create(['name' => 'test', 'count' => 1]);

        $model->summary;

        $model->refresh();
        $this->assertNull($model->materialized);
        $this->assertNotNull($model->custom_column);
        $this->assertArrayHasKey('summary', $model->custom_column);
    }

    /** @test */
    public function handles_invalid_json_safely()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 1]);
        $model->update(['materialized' => 'invalid json']);

        $summary = $model->summary;

        $this->assertIsArray($summary);
        $this->assertEquals('test', $summary['name']);
    }

    /** @test */
    public function caches_payload_during_request()
    {
        $model = TestModel::create(['name' => 'test', 'count' => 1]);

        $summary1 = $model->summary;
        $metrics1 = $model->metrics;

        $model->refresh();
        $materializedData = $model->materialized;

        $summary2 = $model->summary;
        $metrics2 = $model->metrics;

        $this->assertEquals($summary1, $summary2);
        $this->assertEquals($metrics1, $metrics2);
    }
}
