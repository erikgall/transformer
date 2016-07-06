<?php

namespace EGALL\Transformer\Relationships;

use EGALL\Transformer\Relationship;

/**
 * ModelToModelRelationship Class
 *
 * @package EGALL\Transformer\Relationships
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class ModelToModelRelationship extends Relationship
{

    /**
     * Transform a model to model relationship.
     *
     * @return array
     */
    public function transform()
    {
        
        if ($this->isTransformable($model = $this->item->{$this->key})) {

            return $this->transformModel($model);

        }

        return $model->toArray();

    }

    /**
     * Transform a transformable model.
     *
     * @param $model
     * @return mixed
     */
    protected function transformModel($model)
    {

        if ($this->child()) {

            return $model->transformer(true)->with($this->child())->transform();

        }

        return $model->transform();

    }
}