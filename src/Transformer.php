<?php

namespace EGALL\Transformer;

use EGALL\Transformer\Contracts\Transformer as Contract;
use Illuminate\Database\Eloquent\Model;

/**
 * Transformer Class
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class Transformer extends BaseTransformer implements Contract
{

    /**
     * The relationships to be included in the data array.
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * Is the transformation a child transformation.
     *
     * @var bool
     */
    protected $childTransformation;

    /**
     * Transformer constructor.
     *
     * @param Model|\Illuminate\Support\Collection $item
     * @param bool $childTransformation
     */
    public function __construct($item = null, $childTransformation = false)
    {

        parent::__construct($item);

        $this->childTransformation = $childTransformation;

    }

    /**
     * Set the child transformation property.
     *
     * @param bool $childTransformation
     * @return $this
     */
    public function childTransformation(bool $childTransformation)
    {

        $this->childTransformation = $childTransformation;

        return $this;
    }

    /**
     * Set the model to transform.
     *
     * @param Model $model
     * @return Contract
     */
    public function item($model)
    {

        $this->item = $model;

        return $this;
    }

    /**
     * Get or set the model's to lazy load and include in the data array.
     *
     * @return array|\EGALL\Transformer\Transformer
     */
    public function with()
    {

        if (func_num_args() < 1) {

            return $this->relationships;
        }

        return $this->loadRelationships(func_get_args());

    }

    /**
     * @param $name
     */
    protected function addRelationship($name)
    {

        $this->relationships[$name] = RelationshipFactory::build($this->item, $name);

    }

    /**
     * @return $this
     */
    protected function getKeyData()
    {

        foreach ($this->keys as $key) {

            $method = 'get' . ucfirst(camel_case($key)) . 'Attribute';

            $this->set($key, method_exists($this, $method) ? $this->$method($this->item) : $this->item->{$key});

        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function getRelationshipData()
    {

        foreach ($this->relationships as $data) {

            $this->set($data->key(), $data->transform());

        }

        return $this;
    }

    /**
     * Load the relationships.
     *
     * @param array $models
     * @return $this
     */
    protected function loadRelationships(array $models)
    {

        foreach ($models as $key => $load) {

            if (!$this->childTransformation) {

                $this->item->load($load);

            }

            $name = is_string($key) ? $key : $load;

            $this->addRelationship($name);

        }

        return $this;
    }

    /**
     * Get the data in array format.
     *
     * @return array
     */
    protected function toArray()
    {

        return $this->getKeyData()->getRelationshipData()->get();

    }
}