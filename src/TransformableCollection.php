<?php

namespace EGALL\Transformer;

use Illuminate\Support\Collection;
use EGALL\Transformer\Contracts\TransformableCollection as Contract;

class TransformableCollection extends Transformer implements Contract
{

    /**
     * TransformableCollection constructor.
     *
     * @param \Illuminate\Support\Collection $data
     */
    public function __construct(Collection $data)
    {

        parent::__construct($data);

    }

    /**
     * Transform the collection into an array.
     *
     * @return array
     */
    public function transform()
    {

        $data = $this->model->map(function($model) {

            if ($this->isTransformable($model)) {

                return $model->transformer->loadRelationships($this->with)->transform();

            }

            return $model->toArray();

        });

        return $data->toArray();

    }

    /**
     * @return $this|array
     */
    public function with()
    {
        if (func_num_args() == 0) {
            return $this->with;
        }

        $models = func_get_args();

        $this->model->load($models);

        foreach ($models as $name) {

            $this->with[] = new TransformedRelationship($this->model, $name);

        }

        return $this;

    }
}