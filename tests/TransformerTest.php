<?php

use EGALL\Transformer\Relationships\ModelToCollectionRelationship;
use EGALL\Transformer\Relationships\ModelToModelRelationship;

/**
 * Transformer PHPUnit test.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class TransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * The mock model subject  to test.
     *
     * @var \MockModelTransformer
     */
    protected $subject;

    /**
     * The mock eloquent model.
     *
     * @var MockModel
     */
    protected $model;

    /**
     * Setup the testing environment.
     */
    public function setUp()
    {
        $this->model = new MockModel;
        $this->subject = new MockModelTransformer($this->model);
    }

    /**
     * Test transforming a model without relationships.
     *
     * @return void
     */
    public function testTransform()
    {
        $this->assertEquals($this->defaultTransformArray(), $this->subject->transform());
    }

    /**
     * Test transforming with only single relationships.
     *
     * @return void
     */
    public function testTransformingWithOneToRelationships()
    {
        $this->subject->with('model');
        $this->subject->with('models');

        $expected = $this->defaultTransformArray();
        $expected['model'] = $this->defaultTransformArray();
        $expected['models'] = [
            $this->defaultTransformArray(), $this->defaultTransformArray(),
        ];

        $this->assertEquals($expected, $this->subject->transform());
    }

    /**
     * Test transforming nested relationships.
     *
     * @return void
     */
    public function testTransformingWithNestedRelationships()
    {
        $this->subject->with('model.collections.model', 'collections.models');

        $expected = $this->defaultTransformArray();
        $expected['model'] = $this->defaultTransformArray();
        $expected['model']['collections'] = [$this->defaultTransformArray(), $this->defaultTransformArray()];
        $expected['model']['collections'][0]['model'] = $this->defaultTransformArray();
        $expected['model']['collections'][1]['model'] = $this->defaultTransformArray();
        $expected['collections'] = [$this->defaultTransformArray(), $this->defaultTransformArray()];
        $expected['collections'][0]['models'] = [$this->defaultTransformArray(), $this->defaultTransformArray()];
        $expected['collections'][1]['models'] = [$this->defaultTransformArray(), $this->defaultTransformArray()];

        $this->assertEquals($expected, $this->subject->transform());
    }

    /**
     * Test the with method.
     *
     * @return void
     */
    public function testWith()
    {
        $this->assertEquals([], $this->subject->with());

        $this->assertInstanceOf(MockModelTransformer::class, $this->subject->with('course'));

        $this->assertArrayHasKey('course', $this->subject->with());
    }

    /**
     *
     */
    public function testWithModelToModelAndModelToCollection()
    {
        $this->subject->with('model');
        $this->subject->with('models.model');

        $relationships = $this->subject->with();

        $this->assertInstanceOf(ModelToModelRelationship::class, $relationships['model']);
        $this->assertInstanceOf(ModelToCollectionRelationship::class, $relationships['models.model']);
    }

    /**
     * @return array
     */
    protected function defaultTransformArray()
    {
        return [
            'id'   => $this->model->id,
            'name' => $this->model->first_name.' '.$this->model->last_name,
        ];
    }
}
