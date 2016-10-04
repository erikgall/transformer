<?php

namespace EGALL\Transformer;

use Illuminate\Support\Collection;
use EGALL\Transformer\Contracts\CollectionTransformer as Contract;

/**
 * CollectionTransformer Class.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class CollectionTransformer extends BaseTransformer implements Contract
{
    /**
     * Is this transform a nested transformation.
     *
     * @var bool
     */
    protected $childTransformation;

    protected $keys = [];
    /**
     * @var string
     */
    protected $relationships;

    /**
     * CollectionTransformer constructor.
     *
     * @param $items
     * @param bool $childTransformation
     */
    public function __construct($items = null, $childTransformation = false)
    {
        parent::__construct($items);

        $this->childTransformation = $childTransformation;
    }

    /**
     * Set the transformation as a sub-transformation.
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
     * Set the collection to transform.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return $this
     */
    public function collection(Collection $collection)
    {
        $this->item = $collection;

        return $this;
    }

    public function hasAppend()
    {
        return count($this->keys) > 0;
    }
    /**
     * Does the collection have any relationships.
     *
     * @return bool
     */
    public function hasRelationships()
    {
        return count($this->relationships) > 0;
    }

    /**
     * Get the data in array format.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->item->map($this->transformCollectionClosure())->toArray();
    }

    /**
     *
     */
    public function with()
    {
        if (func_num_args() < 1) {
            return $this->relationships;
        }

        $models = func_get_args();

        if (!$this->childTransformation) {
            $this->item->load($models);
        }

        foreach ($models as $key => $name) {
            if (is_string($key)) {
                $this->relationships[] = $key;
            } else {
                $this->relationships[] = $name;
            }
        }

        return $this;
    }

    protected function transformCollectionClosure()
    {
        return function ($model) {
            if ($this->isTransformable($model)) {
                if ($this->hasRelationships()) {
                    $transformer = $model->transformer(true);

                    if ($this->hasAppend()) {
                        $transformer->keys($this->keys);
                    }

                    foreach ($this->relationships as $relationship) {
                        $transformer->with($relationship);
                    }

                    return $transformer->transform();
                }

                if ($this->hasAppend()) {
                    return $model->transformer()->keys($this->keys)->transform();
                }

                return $model->transform();
            }

            return $model->toArray();
        };
    }
}
