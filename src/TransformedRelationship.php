<?php

namespace EGALL\Transformer;

use Illuminate\Support\Collection;
use EGALL\Transformer\Contracts\Transformable;

/**
 * TransformedRelationship Class
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class TransformedRelationship
{

    /**
     * The child relationships passed in using dot notation.
     *
     * @var string
     */
    protected $child;

    /**
     * The relationship model key to access the children.
     *
     * @var string
     */
    protected $key;

    /**
     * The parent model object.
     *
     * @var string
     */
    protected $model;

    /**
     * Is the relationship a collection.
     *
     * @var bool
     */
    protected $collection;

    /**
     * Is the relationship from a transformable model.
     *
     * @var string
     */
    protected $transformable;

    /**
     * TransformedRelationship constructor.
     *
     * @param $model
     * @param $child
     */
    public function __construct($model, $child)
    {

        $this->model = $model;
        $this->child = $child;
        $this->getRelationshipKeyName();
    }

    /**
     * Does the relationship have any children.
     *
     * @return bool
     */
    public function hasChildRelationship()
    {

        return $this->key !== $this->child;

    }

    /**
     * Is the relationship a collection.
     *
     * @return bool
     */
    public function isCollection()
    {

        if (is_null($this->collection)) {

            if ($this->modelIsCollection() && !$this->model->isEmpty()) {
                $this->collection = $this->model->first()->{$this->key} instanceof Collection;

                return $this->collection;
            }

            if ($this->model->{$this->key()} instanceof Collection) {

                $this->collection = true;
            }

        }

        return $this->collection;

    }

    /**
     * Check if the related key is transformable.
     *
     * @return bool
     */
    public function isTransformable()
    {

        if (is_null($this->transformable)) {

            if ($this->modelIsCollection() && !$this->model->isEmpty()) {

                $model = $this->model->first()->{$this->key};

            } else {

                $model = $this->model->{$this->key};

            }

            $this->transformable = in_array(
                Transformable::class, class_implements($model)
            );

        }

        return $this->transformable;

    }

    /**
     * The relationship key.
     *
     * @return string
     */
    public function key()
    {

        return $this->key;

    }

    /**
     * Is the parent model a collection.
     *
     * @return bool
     */
    public function modelIsCollection()
    {

        return $this->model instanceof Collection;

    }

    /**
     * Return the transformed relationship.
     *
     * @return array
     */
    public function transform()
    {

        if ($this->modelIsCollection()) {

            return $this->model->map(function ($model) {

                if ($this->isCollection()) {

                    $model->{$this->key}->map(function ($child) {

                        if ($this->hasChildRelationship()) {

                            return $child->transformer()->with($this->child)->tranform();

                        }

                        return $child->transform();

                    });

                }


                if ($this->hasChildRelationship()) {

                    return $model->{$this->key}->transformer()->with($this->child)->transform();

                }

                return $model->{$this->key}->transform();


            })->toArray();

        }

        if ($this->isCollection()) {

            return $this->transformCollection();

        }

        if ($this->isTransformable()) {

            return $this->transformableToArray($this->model->{$this->key});

        }

        return $this->model->{$this->key}->toArray();
    }

    /**
     * Get the related model's property name.
     *
     * @return string
     */
    protected function getRelationshipKeyName()
    {

        if (!str_contains($this->child, '.')) {

            $this->key = $this->child;

            return;
        }

        $this->setChildAndModelKey();

    }

    /**
     * @return mixed
     */
    protected function transformCollection()
    {

        return $this->model->{$this->key}->map(function ($model) {

            return $this->hasChildRelationship() ?
                $model->transformer()->with($this->child)->transform() : $model->transform();

        })->toArray();

    }

    /**
     * Transform a transformable model to an array.
     *
     * @param \EGALL\Transformer\Contracts\Transformable|\Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    protected function transformableToArray($model)
    {

        if ($this->hasChildRelationship()) {

            $model = $model->transformer()->with($this->child);

        }

        return $model->transform();
    }

    /**
     * Set the child key and get the relationship key.
     *
     * @return mixed
     */
    protected function setChildAndModelKey()
    {

        $parts = explode('.', $this->child);

        $this->key = array_first($parts);

        array_forget($parts, 0);

        $this->child = implode('.', $parts);

    }
}