<?php

namespace EGALL\Transformer\Contracts;

/**
 * TransformableCollection interface contract.
 *
 * @package EGALL\Transformer\Contracts
 * @author Erik Galloway <erik@mybarnapp.com>
 */
interface TransformableCollection
{

    /**
     * Transform a collection.
     *
     * @return array
     */
    public function transform();

    /**
     * Lazy load a model relationship.
     *
     * @return $this
     */
    public function with();

}