<?php

namespace EGALL\Transformer\Relationships;

use EGALL\Transformer\Relationship;

/**
 * CollectionToCollectionRelationship Class.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class CollectionToCollectionRelationship extends Relationship
{
    /**
     * Get the relationship's transformation array.
     *
     * @return array
     */
    public function transform()
    {
        if ($this->isTransformable($this->item->first()->{$this->key}->first())) {
            $this->transformCollection();
        }

        return $this->transformChildToArray();
    }

    /**
     * Map the collection's model belonging the the child collection
     * to an array.
     *
     * @return mixed
     */
    protected function transformChildToArray()
    {
        return $this->item->map(function ($model) {
            return $model->map(function ($submodel) {
                return $submodel->toArray();
            })->toArray();
        })->toArray();
    }

    /**
     * Transform a collection's transform model belong to the child collection.
     *
     * @return array
     */
    protected function transformCollection()
    {
        return $this->item->map(function ($model) {
            return $model->map(function ($submodel) {
                if ($this->child) {
                    return $submodel->transformer(true)->with($this->child)->transform();
                }

                return $submodel->transform();
            })->toArray();
        })->toArray();
    }
}
