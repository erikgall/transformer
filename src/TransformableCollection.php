<?php

namespace EGALL\Transformer;

use Illuminate\Support\Collection;

class TransformableCollection extends Transformer
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

                return $model->transform();

            }

            return $model->toArray();

        });

        return $data->toArray();

    }

    public function with()
    {
        if (func_num_args() == 0) {
            return $this->with;
        }

        $models = func_get_args();

        $this->model->each(function($model) use ($models) {

            $model->load($models);

        });

        foreach ($models as $name) {

            $this->with[] = $name;

        }

        return $this;

    }
}