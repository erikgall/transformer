<?php

namespace EGALL\Transformer\Relationships;

use EGALL\Transformer\Relationship;

/**
 * ModelToCollectionRelationship Class
 *
 * @package EGALL\Transformer\Relationships
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class ModelToCollectionRelationship extends Relationship
{

    /**
     * Transform a model to collection relationship.
     *
     * @return array
     */
    public function transform()
    {
        $model = $this->item->{$this->key};

        if ($this->isTransformable($model->first())) {

            return $this->transformCollection($model);

        }

        return $this->collectionToArray($model);
    }

    /**
     * Map a collection calling toArray() on each item.
     *
     * @param $model
     * @return array
     */
    protected function collectionToArray($model)
    {

        return $model->map(function($item) {

            return $item->toArray();

        })->toArray();


    }

    /**
     * Transform a transformable collection.
     *
     * @param $model
     * @return array
     */
    protected function transformCollection($model)
    {

        return $model->map(function($child) {

            if ($this->child) {

                return $child->transformer(true)->with($this->child)->transform();

            }

            return $child->transform();

        })->toArray();

    }
}