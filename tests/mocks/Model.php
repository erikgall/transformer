<?php

use EGALL\Transformer\Contracts\Transformable;
use Illuminate\Support\Collection;

class Model implements Transformable
{

    public $id = 1;

    public $first_name = 'First';

    public $last_name = 'Last Name';

    public $password = 'password';

    public function load($models)
    {

        if (!is_array($models)) {
            $models = (array) $models;
        }

        foreach ($models as $key) {

            if (str_contains($key, '.')) {

                $this->loadNestedRelationship($key);

            } else {

                $this->{$key} = $this->getRelationshipModels($key);

            }


        }

    }

    /**
     * @return array
     */
    public function transform()
    {

        return $this->transformer()->transform();
    }

    /**
     * @return \ModelTransformer
     */
    public function transformer()
    {

        return new ModelTransformer($this);
    }

    protected function loadNestedRelationship($key)
    {

        $parts = explode('.', $key);

        $related = array_first($parts);

        array_forget($parts, 0);

        $children = implode('.', $parts);

        $this->{$related} = $this->getRelationshipModels($related);

        if ($this->{$related} instanceof Collection) {


        } else {

            $this->{$related}->load($children);
            $this->{$related}->each(function($model) use ($children) {

                $model->load($children);

            });
        }


    }

    protected function getRelationshipModels($key)
    {

        if (str_plural($key) == $key) {

            return collect([new static, new static]);

        }

        return new static;


    }
}