<?php

namespace EGALL\Transformer;

use EGALL\Transformer\Contracts\Transformable;
use Illuminate\Support\Collection;

/**
 * BaseTransformer Class
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
     * The relationships to be included in the data array.
     * 
     * @var array
     */
    protected $relationships = [];

    /**
     * BaseTransformer constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {

        $this->item = $item;

    }

    /**
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

        $this->keys = count($this->keys) ? array_merge($this->keys, $keys) : $keys;

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
     * Get the data in array format.
     *
     * @return array
     */
    abstract public function toArray();

}