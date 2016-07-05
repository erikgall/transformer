<?php

namespace EGALL\Transformer\Contracts;

/**
 * Base eloquent model transformer.
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
/**
 * Transformer interface contract.
 *
 * @package EGALL\Transformer\Contracts
 * @author Erik Galloway <erik@mybarnapp.com>
 */
interface Transformer
{

    /**
     * Get the transformed data array.
     *
     * @param string|null $key
     * @return array
     */
    public function get($key = null);

    /**
     * Get the transformed array keys.
     *
     * @return array
     */
    public function getKeys();

    /**
     * Check if the model is actually a collection.
     *
     * @param $object
     * @return bool
     */
    public function isCollection($object);

    /**
     * Check if the model implements the transformable contract.
     *
     * @param $object
     * @return bool
     */
    public function isTransformable($object);

    /**
     * Set the model or collection.
     * 
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection $model
     * @return $this
     */
    public function model($model);

    /**
     * Set a key in the transformed array.
     *
     * @param string $key
     * @param $value
     * @return \EGALL\Transformer\Transformer
     */
    public function set($key, $value);

    /**
     * Set the model keys to be used when transforming.
     *
     * @param array $keys
     * @return \EGALL\Transformer\Transformer
     */
    public function setKeys(array $keys);

    /**
     * Transform the model into an array.
     *
     * @return array
     */
    public function transform();

    /**
     * Transform a collection.
     *
     * @return array
     */
    public function transformCollection();

    /**
     * Lazy load a model relationship.
     *
     * @return \EGALL\Transformer\Transformer
     */
    public function with();
}