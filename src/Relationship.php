<?php

namespace EGALL\Transformer;

/*
 * Relationship Class
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
use EGALL\Transformer\Contracts\Transformable;

/**
 * Relationship Class.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
abstract class Relationship
{
    /**
     * The child model to transform.
     *
     * @var mixed
     */
    protected $child;

    /**
     * @var string
     */
    protected $item;

    /**
     * @var string
     */
    protected $key;

    /**
     * Relationship constructor.
     *
     * @param $item
     * @param $key
     */
    public function __construct($item, $key)
    {
        $this->item = $item;
        $this->key = $key;
        $this->child = $this->getChildName($key);
    }

    /**
     * Get the child key name.
     *
     * @return mixed
     */
    public function child()
    {
        return $this->child;
    }

    /**
     * Check if an object implements transformable.
     *
     * @param $object
     * @return bool
     */
    public function isTransformable($object)
    {
        return in_array(Transformable::class, class_implements($object));
    }

    /**
     * Get the relationship key name.
     *
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Get the relationship's transformation array.
     *
     * @return array
     */
    abstract public function transform();

    /**
     * Forget the first array item and implode the items to dot notation.
     *
     * @param array $parts
     * @return string
     */
    protected function forgetFirstAndImplodeToDotNotation(array $parts)
    {
        array_forget($parts, 0);

        return implode('.', $parts);
    }

    /**
     * Get the child model relationship key name and update the key name.
     *
     * @param $key
     * @return mixed
     */
    protected function getChildName($key)
    {
        if (!str_contains($key, '.')) {
            return;
        }

        $parts = explode('.', $key);

        $this->key = array_first($parts);

        return $this->forgetFirstAndImplodeToDotNotation($parts);
    }
}
