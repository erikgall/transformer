<?php

use EGALL\Transformer\BaseTransformer;

/**
 * BaseTransformerTest Class
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class BaseTransformerTest extends PHPUnit_Framework_TestCase
{

    /**
     * The test subject.
     *
     * @var \EGALL\Transformer\BaseTransformer
     */
    protected $subject;

    /**
     *
     */
    public function setUp()
    {

        $this->subject = new MockBaseTransformer(new Model());

    }
    /**
     * Test checking the item is a collection.
     *
     * @return void
     */
    public function testIsCollection()
    {

        $this->assertFalse($this->subject->isCollection());
        $this->assertTrue($this->subject->isCollection(collect([])));

    }

    /**
     * Test checking if the item is transformable.
     *
     * @return void
     */
    public function testIsTransformable()
    {

        $this->assertTrue($this->subject->isTransformable());
        $this->assertFalse($this->subject->isTransformable(collect([])));

    }

    /**
     * Test getting and setting the keys.
     *
     * @return void
     */
    public function testKeys()
    {

        // Returns the keys array if nothing is passed in.
        $this->assertEmpty($this->subject->keys());

        // Returns $this if setting keys.
        $this->assertInstanceOf(BaseTransformer::class, $this->subject->keys(['id', 'name']));
        $this->assertEquals(['id', 'name'], $this->subject->keys());

        // If the keys are not empty it merges instead of replacing the keys
        $this->assertEquals(['id', 'name', 'lang'], $this->subject->keys(['lang'])->keys());

    }

}
