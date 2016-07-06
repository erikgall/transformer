<?php

namespace EGALL\Transformer;

use EGALL\Transformer\Contracts\Transformable;
use EGALL\Transformer\Exceptions\PropertyDoesNotExist;
use Illuminate\Support\Collection;

/**
 * Base transformer class.
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
abstract class BaseTransformer
{

    /**
     * The transformed item data array.
     * 
     * @var array
     */
    protected $data = [];

    /**
     * The item to be transformed.
     * 
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    protected $item;

    /**
     * The keys in be included in the data array.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * BaseTransformer constructor.
     *
     * @param $item
     */
    public function __construct($item = null)
    {

        $this->item = $item;

    }

    /**
     * Get the data array or a data array key.
     * 
     * @param null|string $key
     * @return array|mixed
     */
    public function get($key = null)
    {

        if (is_null($key)) {
            
            return $this->data;
            
        }
        
        return $this->data[$key];
        
    }

    /**
     * Check if an item is a collection.
     * 
     * @param null $item
     * @return bool
     */
    public function isCollection($item = null)
    {

        if (is_null($item)) {

            $item = $this->item;

        }

        return ($item instanceof Collection);

    }

    /**
     * Check if an item implements the transformable contract.
     * 
     * @param null $item
     * @return bool
     */
    public function isTransformable($item = null)
    {

        if (is_null($item)) {

            $item = $this->item;
        }

        return in_array(Transformable::class, class_implements($item));

    }

    /**
     * Get or set the transformer keys.
     *
     * @param null $keys
     * @return $this|array
     */
    public function keys($keys = null)
    {

        if (is_null($keys)) {

            return $this->keys;

        }

        if (!is_array($keys)) {
            $keys = (array) $keys;
        }

        $this->keys = count($this->keys) ? array_merge($this->keys, $keys) : $keys;

        return $this;
    }

    /**
     * Set a key value pair.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {

        $this->data[$key] = $value;
        
        return $this;
    }

    /**
     * Transform the item to an array.
     *
     * @return array
     */
    public function transform()
    {

        return $this->toArray();

    }

    /**
     * Allow dynamic access the retrieving data keys.
     *
     * @param string $key
     * @return mixed
     * @throws \EGALL\Transformer\Exceptions\PropertyDoesNotExist
     */
    public function __get($key)
    {

        if (array_key_exists($key, $this->data)) {

            return $this->data[$key];

        }

        throw new PropertyDoesNotExist("The property {$key} does not exist in the data array.");

    }

    /**
     * Allow dynamic access to the data array.
     *
     * @param string $key
     * @param $value
     */
    public function __set($key, $value)
    {

        $this->set($key, $value);

    }

    /**
     * Get the data in array format.
     *
     * @return array
     */
    abstract protected function toArray();


}