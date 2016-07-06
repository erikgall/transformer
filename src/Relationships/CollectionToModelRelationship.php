<?php

namespace EGALL\Transformer\Relationships;

use EGALL\Transformer\Relationship;

/**
 * CollectionToModelRelationship Class
 *
 * @package EGALL\Transformer\Relationships
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class CollectionToModelRelationship extends Relationship
{

    /**
     * Get the relationship transformation array.
     *
     * @return array
     */
    public function transform()
    {
        if ($this->isTransformable($this->item->first())) {

            return $this->transformCollectionModel();
        }

        return $this->transformCollectionToArray();
    }

    /**
     * Transform a collection's related model.
     *
     * @return array
     */
    protected function transformCollectionModel()
    {

        return $this->item->map(function($model) {

            if ($this->child) {

                return $model->{$this->key}->transformer(true)->with($this->child)->transform();

            }

            return $model->{$this->key}->transform();

        });
    }

    /**
     * Map a collection calling toArray() on each model.
     *
     * @return array
     */
    protected function transformCollectionToArray()
    {

        return $this->item->map(function($model) {

            return $model->{$this->key}->toArray();

        })->toArray();
    }
}