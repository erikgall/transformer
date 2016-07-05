<?php

namespace EGALL\Transformer\Traits;

use EGALL\Transformer\Transformer;

/**
 * TransformableModel Class
 *
 * @package EGALL\Transformer\Traits
 * @author Erik Galloway <erik@mybarnapp.com>
 */
trait TransformableModel
{

    /**
     * Transform the model to an array.
     *
     * @return array
     */
    public function transform()
    {

        return $this->transformer()->transform();

    }

    /**
     * Get the model's transformer.
     *
     * @return \EGALL\Transformer\Transformer
     */
    public function transformer()
    {

        $transformer = $this->transformerClass();

        return new $transformer($this);

    }

    /**
     * Get the transformer class.
     *
     * Transformer class can be setting a transformer
     * property or by overriding the transformer method.
     *
     * @return string
     */
    protected function transformerClass()
    {

        if (property_exists($this, 'transformer')) {

            return $this->transformer;

        }

        return Transformer::class;
    }
}