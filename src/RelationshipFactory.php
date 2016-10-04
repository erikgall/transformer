<?php

namespace EGALL\Transformer;

use EGALL\Transformer\Relationships\CollectionToCollectionRelationship;
use EGALL\Transformer\Relationships\CollectionToModelRelationship;
use EGALL\Transformer\Relationships\ModelToCollectionRelationship;
use EGALL\Transformer\Relationships\ModelToModelRelationship;
use Illuminate\Support\Collection;

/**
 * RelationshipFactory Class.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class RelationshipFactory
{
    /**
     * Is the parent item a collection.
     *
     * @var bool
     */
    protected $collection;

    /**
     * Is the child item a collection.
     *
     * @var bool
     */
    protected $childCollection;

    /**
     * The parent item or collection.
     *
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    protected $item;

    /**
     * The relationship key used to lazy load the models.
     *
     * @var string
     */
    protected $key;

    /**
     * RelationshipFactory constructor.
     *
     * @param $item
     * @param $key
     * @param bool $collection
     * @param bool $childCollection
     */
    private function __construct($item, $key, $collection = false, $childCollection = false)
    {
        $this->item = $item;
        $this->key = $key;
        $this->collection = $collection;
        $this->childCollection = $childCollection;
    }

    /**
     * Make a new relationship.
     *
     * @return \EGALL\Transformer\Relationship
     */
    public function make()
    {
        if ($this->collection) {
            return $this->makeCollectionToRelationship();
        }

        return $this->makeModelToRelationship();
    }

    /**
     * Make a collection to model/collection relationship.
     *
     * @return \EGALL\Transformer\CollectionToCollectionRelationship|\EGALL\Transformer\CollectionToModelRelationship
     */
    protected function makeCollectionToRelationship()
    {
        if ($this->childCollection) {
            return new CollectionToCollectionRelationship($this->item, $this->key);
        }

        return new CollectionToModelRelationship($this->item, $this->key);
    }

    /**
     * Make a model to model/collection relationship.
     *
     * @return \EGALL\Transformer\ModelToCollectionRelationship|\EGALL\Transformer\ModelToModelRelationship
     */
    protected function makeModelToRelationship()
    {
        if ($this->childCollection) {
            return new ModelToCollectionRelationship($this->item, $this->key);
        }

        return new ModelToModelRelationship($this->item, $this->key);
    }

    /**
     * @param $item
     * @param $key
     * @return mixed
     */
    public static function build($item, $key)
    {
        if (static::isCollection($item)) {
            return static::makeCollectionRelationship($item, $key);
        }

        return static::makeModelRelationship($item, $key);
    }

    /**
     * @param $key
     * @return mixed
     */
    protected static function getRelationshipName($key)
    {
        return str_contains($key, '.') ? array_first(explode('.', $key)) : $key;
    }

    /**
     * @param $item
     * @param $key
     * @return mixed
     */
    protected static function makeCollectionRelationship($item, $key)
    {
        $name = static::getRelationshipName($key);

        if ($item->first(static::hasCollectionCallback($name))) {
            return (new static($item, $key, true, true))->make();
        }

        return (new static($item, $key, true, false))->make();
    }

    /**
     * @param $item
     * @param $key
     * @return mixed
     */
    protected static function makeModelRelationship($item, $key)
    {
        $name = static::getRelationshipName($key);

        if (static::isCollection($item->{$name})) {
            return (new static($item, $key, false, true))->make();
        }

        return (new static($item, $key))->make();
    }

    /**
     * @param $item
     * @return bool
     */
    protected static function isCollection($item)
    {
        return $item instanceof Collection;
    }

    /**
     * @param $key
     * @return \Closure
     */
    protected static function hasCollectionCallback($key)
    {
        return function ($model) use ($key) {
            return static::isCollection($model->{$key});
        };
    }
}
