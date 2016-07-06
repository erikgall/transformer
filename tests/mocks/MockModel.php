<?php

use EGALL\Transformer\Contracts\Transformable;

/**
 * MockModel Class
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class MockModel implements Transformable
{

    public $id = 1;
    public $first_name = 'First';
    public $last_name = 'Last Name';
    public $password = 'password';

    /**
     * Simulation lazy loading a model.
     * 
     * @param $models
     */
    public function load($models)
    {

        if (!is_array($models)) {
            $models = (array) $models;
        }

        $this->loadRelationships($models);

    }

    /**
     * Convert the model to an array.
     * 
     * @return array
     */
    public function toArray()
    {

        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'password'   => $this->password
        ];
    }

    /**
     * Transform the model.
     *
     * @return array
     */
    public function transform()
    {

        return $this->transformer()->transform();
    }

    /**
     * Get the model transformer.
     *
     * @return \ModelTransformer
     */
    public function transformer()
    {

        return new MockModelTransformer($this);
    }

    /**
     * Check if a string equals is plural form.
     *
     * @param $string
     * @return bool
     */
    protected function isPlural($string)
    {

        return str_plural($string) == $string;

    }

    /**
     * Get a child relationship name.
     *
     * @param $name
     * @return string
     */
    protected function getChildRelationship($name)
    {

        $parts = explode('.', $name);
        array_forget($parts, 0);

        return implode('.', $parts);
    }

    /**
     * Get a nested relationship first key name.
     *
     * @param string $name
     * @return string
     */
    protected function getNestedKeyName($name)
    {

        return array_first(explode('.', $name));

    }

    /**
     * Check if the model is a nested relationship.
     *
     * @param $name
     * @return bool
     */
    protected function isNestedRelationship($name)
    {

        return str_contains($name, '.');
    }

    /**
     * Loop through a relationship array.
     *
     * @param array $models
     */
    protected function loadRelationships(array $models)
    {

        foreach ($models as $model) {

            $this->setRelatedModels($model);

        }

    }

    /**
     * Set a non nested relationship key.
     *
     * @param $related
     */
    protected function setRelationship($related)
    {

        if ($this->isPlural($related)) {

            $this->{$related} = collect([new static, new static]);

        } else {

            $this->{$related} = new static;
        }
    }

    /**
     * Set a nested relationship key and load the child relationships.
     *
     * @param string $related
     */
    protected function setNestedRelationship($related)
    {

        $key = $this->getNestedKeyName($related);

        $children = $this->getChildRelationship($related);

        if ($this->isPlural($key)) {

            $this->{$key} = collect([new static, new static]);

            $this->{$key}->each(function ($model) use ($children) {

                $model->load($children);

            });

        } else {

            $this->{$key} = new static;

            $this->{$key}->load($children);

        }
    }

    /**
     * Set a string equal to a new relationship.
     *
     * @param $related
     */
    protected function setRelatedModels($related)
    {

        if ($this->isNestedRelationship($related)) {

            $this->setNestedRelationship($related);

        } else {

            $this->setRelationship($related);

        }
    }
}