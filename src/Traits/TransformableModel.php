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
     * @param bool $nested
     * @return \EGALL\Transformer\Transformer
     */
    public function transformer($nested = false)
    {

        $transformer = $this->transformerClass();

        return new $transformer($this, $nested);

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

        if (class_exists($default = $this->getDefaultTransformerNamespace())){

            return $default;
        }

        if (property_exists($this, 'transformer')) {

            return $this->transformer;

        }

        return Transformer::class;
    }

    /**
     * Get the default transformer class for the model.
     *
     * @return string
     */
    protected function getDefaultTransformerNamespace()
    {

        $parts = explode('\\', get_class($this));

        $class = array_last($parts);

        array_forget($parts, array_last(array_keys($parts)));

        return $this->makeTransformer($class, implode('\\', $parts));

    }

    /**
     * Make the default transformer class namespace.
     *
     * @param string $class
     * @param string $namespace
     * @return string
     */
    protected function makeTransformer($class, $namespace)
    {

        return $namespace . '\\Transformers\\' . $class . 'Transformer';
    }
}