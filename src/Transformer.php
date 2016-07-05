<?php

namespace EGALL\Transformer;

use EGALL\Transformer\Contracts\Transformer as TransformerContract;
use Illuminate\Support\Collection;
use EGALL\Transformer\Contracts\Transformable;
use EGALL\Transformer\Exceptions\PropertyDoesNotExist;

/**
 * Base eloquent model transformer.
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class Transformer implements TransformerContract
{

    /**
     * The transformed data array to return.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The keys to include in the transformed model.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * The model or model collection to be transformed.
     *
     * @var \Illuminate\Database\Eloquent\Model|Collection
     */
    protected $model;

    /**
     * The model relationships to be included in the transformed array.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Transformer constructor.
     *
     * @param $model
     */
    public function __construct($model = null)
    {

        $this->model = $model;

    }

    /**
     * Get the transformed data array.
     *
     * @param string|null $key
     * @return array
     */
    public function get($key = null)
    {

        if (is_null($key)) {

            return $this->data;

        }

        return $this->data[$key];

    }

    /**
     * Get the transformed array keys.
     *
     * @return array
     */
    public function getKeys()
    {

        return $this->keys;

    }

    /**
     * Check if the model is actually a collection.
     *
     * @param $object
     * @return bool
     */
    public function isCollection($object)
    {

        return ($object instanceof Collection);

    }

    /**
     * Check if the model implements the transformable contract.
     *
     * @param $object
     * @return bool
     */
    public function isTransformable($object)
    {

        return in_array(Transformable::class, class_implements($object));

    }

    /**
     * Set the with array so collections don't see n+1 problem.
     * 
     * @param array $relationships
     * @return $this
     */
    public function loadRelationship(array $relationships)
    {
        
        $this->with = $relationships;

        return $this;
        
    }

    /**
     * Set the model or collection.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection $model
     * @return $this
     */
    public function model($model)
    {

        if ($this->isCollection($model)) {

            return new TransformableCollection($model);

        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set a key in the transformed array.
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
     * Set the model keys to be used when transforming.
     *
     * @param array $keys
     * @return $this
     */
    public function setKeys(array $keys)
    {

        $this->keys = $keys;

        return $this;

    }

    /**
     * Transform the model into an array.
     *
     * @return array
     */
    public function transform()
    {

        return $this->isCollection($this->model) ?
            $this->transformCollection() : $this->toArray();

    }

    /**
     * Transform a collection.
     *
     * @return array
     */
    public function transformCollection()
    {

        $collection = $this->model->map(function ($model) {

            if ($this->isTransformable($model)) {

                return $model->transform();

            }

            return $model->toArray();

        });

        return $collection->toArray();
    }

    /**
     * Lazy load a model relationship.
     *
     * @return $this
     */
    public function with()
    {

        if (func_num_args() == 0) {
            return $this->with;
        }

        $this->model->load($models = func_get_args());

        foreach ($models as $name) {

            $this->with[] = new TransformedRelationship($this->model, $name);

        }

        return $this;
    }

    /**
     * Allow magic access to the data array.
     *
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {

        if (array_key_exists($key, $this->data)) {

            return $this->data[$key];

        }

        throw new PropertyDoesNotExist("The property {$key} does not exist in the data array.");

    }

    /**
     * Allow data attributes to be set magically.
     *
     * @param string $key
     * @param $value
     */
    public function __set($key, $value)
    {

        $this->set($key, $value);

    }

    /**
     * Check if an array key exists.
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {

        return array_key_exists($key, $this->data);

    }

    /**
     * Get the attribute method name.
     *
     * @param string $key
     * @return string
     */
    protected function getAttributeMethodName($key)
    {

        return 'get' . ucfirst(camel_case($key)) . 'Attribute';
    }

    /**
     * Set the data from the keys.
     *
     * @return $this
     */
    protected function setDataFromKeys()
    {

        foreach ($this->keys as $key) {

            if (method_exists($this, $method = $this->getAttributeMethodName($key))) {

                $this->{$key} = $this->$method();

            } else {

                $this->{$key} = $this->model->{$key};

            }

        }

        return $this;

    }

    /**
     * @return $this
     */
    protected function setRelationshipData()
    {

        foreach ($this->with as $relationship) {

            $this->{$relationship->key()} = $relationship->transform();

        }

        return $this;

    }

    /**
     * Transform the model into an array.
     *
     * @return array
     */
    protected function toArray()
    {

        return $this->setDataFromKeys()->setRelationshipData()->get();

    }

}