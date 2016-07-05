<?php

/**
 * Transformer PHPUnit test
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class TransformerTest extends PHPUnit_Framework_TestCase
{

    /**
     * The mock model subject  to test.
     *
     * @var \Model
     */
    protected $subject;

    protected $model;

    /**
     * Setup the testing environment.
     */
    public function setUp()
    {

        $this->model = new Model;
        $this->subject = new ModelTransformer($this->model);

    }

    /**
     * Test checking if the model is a collection.
     *
     * @return void
     */
    public function testIsCollection()
    {

        $this->assertFalse($this->subject->isCollection($this->model));

        $this->assertTrue($this->subject->isCollection(collect([$this->model])));

    }

    /**
     * Test checking if an object implements the transformable contract.
     *
     * @return void
     */
    public function testIsTransformable()
    {

        $this->assertTrue($this->subject->isTransformable($this->model));
        $this->assertFalse($this->subject->isTransformable(ModelTransformer::class));

    }

    /**
     * Test setting a key, value pair.
     *
     * @return void
     */
    public function testSet()
    {

        $this->subject->set('lang', 'en');

        $data = $this->subject->transform();

        $this->assertArrayHasKey('lang', $data);
        $this->assertEquals('en', $data['lang']);

    }

    /**
     * Test transforming the model.
     *
     * @return void
     */
    public function testTransform()
    {

        $this->assertEquals($this->transformedModelArray(), $this->subject->transform());

    }

    /**
     * Test transforming a collection.
     *
     * @return void
     */
    public function testTransformCollection()
    {

        $collection = collect([$this->model, new Model()]);

        $expected = [$this->transformedModelArray(), $this->transformedModelArray()];

        $this->assertEquals($expected, (new ModelTransformer($collection))->transform());
    }

    /**
     * Test lazy loading a model.
     *
     * @return void
     */
    public function testLazyLoadingASingularRelationship()
    {

        $expected = $this->transformedModelArray();
        $expected['school'] = $this->transformedModelArray();

        $this->assertEquals($expected, $this->subject->with('school')->transform());

    }

    public function testLazyLoadingAHasManyRelationship()
    {

        $expected = $this->transformedModelArray();
        $expected['schools'] = [$this->transformedModelArray(), $this->transformedModelArray()];

        $this->assertEquals($expected, $this->subject->with('schools')->transform());

    }

    public function testLazyLoadingANestedRelationship()
    {

        $expected = $this->transformedModelArray();
        $expected['schools'][] = $this->transformedModelArray();
        $expected['schools'][] = $this->transformedModelArray();
        $expected['schools'][0]['course'] = $this->transformedModelArray();
        $expected['schools'][1]['course'] = $this->transformedModelArray();
        $actual = $this->subject->with('schools.course')->transform();
        $this->assertEquals($expected, $actual);

    }

    protected function transformedModelArray()
    {

        return [
            'id'   => $this->model->id,
            'name' => $this->model->first_name . ' ' . $this->model->last_name
        ];
    }
}