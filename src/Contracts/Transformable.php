<?php

namespace EGALL\Transformer\Contracts;

/**
 * Transformable interface contract.
 *
 * @package EGALL\Transformer\Contracts
 * @author Erik Galloway <erik@mybarnapp.com>
 */
interface Transformable
{

    /**
     * Transform the model to an array.
     *
     * @return array
     */
    public function transform();

    /**
     * Get the model transformer.
     *
     * @return \EGALL\Transformer\Transformer
     */
    public function transformer();
}