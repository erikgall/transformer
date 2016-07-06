<?php

use EGALL\Transformer\CollectionTransformer;

/**
 * CollectionTransformerTest Class
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class CollectionTransformerTest extends PHPUnit_Framework_TestCase
{

    /**
     * The collection of model's.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $collection;
    
    /**
     * The testing subject.
     *
     * @var CollectionTransformer
     */
    protected $subject;

    /**
     * Setup the testing environment.
     *
     * @return void
     */
    public function setUp()
    {

        $this->collection = new MockCollection([$this->newModel(), $this->newModel()]);
        $this->subject = new CollectionTransformer($this->collection, true);

    }

    /**
     * Test appending keys to a collection.
     *
     * @return void
     */
    public function testAppendingKeysToACollection()
    {

        $expected = [$this->defaultTransformArray(), $this->defaultTransformArray()];
        $expected[0]['initials'] = 'FL';
        $expected[1]['initials'] = 'FL';

        $actual = $this->subject->keys('initials')->transform();

        $this->assertEquals($expected, $actual);

    }

    /**
     * Test transforming a collection.
     *
     * @return void
     */
    public function testTransform()
    {

        $transformedArray = $this->defaultTransformArray();

        $expected = [$transformedArray, $transformedArray];

        $this->assertEquals($expected, $this->subject->transform());

    }

    /**
     * Test loading relationships
     *
     * @return void
     */
    public function testTransformingWithRelationships()
    {
        $transformedArray = $this->defaultTransformArray();

        $expected = [$transformedArray, $transformedArray];
        $expected[0]['model'] = $transformedArray;
        $expected[1]['model'] = $transformedArray;

        $this->assertEquals($expected, $this->subject->with('model')->transform());

    }

    /**
     * Test transforming a nested relationships.
     *
     * @return void
     */
    public function testTransformingWithNestedRelationships()
    {

        $transformedArray = $this->defaultTransformArray();

        $collection = [$transformedArray, $transformedArray];
        $expected = $collection;
        $expected[0]['models'] = $collection;

        $expected[0]['models'] = $collection;
        $expected[0]['models'][0] = $transformedArray;
        $expected[0]['models'][0]['collections'] = $collection;

        $expected[0]['models'][0]['collections'][0] = $transformedArray;
        $expected[0]['models'][0]['collections'][0]['model'] = $transformedArray;
        $expected[0]['models'][0]['collections'][1] = $transformedArray;
        $expected[0]['models'][0]['collections'][1]['model'] = $transformedArray;

        $expected[0]['models'][1]['collections'][0] = $transformedArray;
        $expected[0]['models'][1]['collections'][0]['model'] = $transformedArray;
        $expected[0]['models'][1]['collections'][1] = $transformedArray;
        $expected[0]['models'][1]['collections'][1]['model'] = $transformedArray;
        $expected[1] = $expected[0];

        $this->assertEquals($expected, $this->subject->with('models.collections.model')->transform());

    }

    /**
     * The default transformed model array.
     *
     * @return array
     */
    protected function defaultTransformArray()
    {

        $model = $this->newModel();

        return [
            'id'   => $model->id,
            'name' => $model->first_name . ' ' . $model->last_name
        ];
    }


    /**
     * Get a new mock model.
     *
     * @return \MockModel
     */
    protected function newModel()
    {

        return new MockModel();

    }
}
