<?php

namespace EGALL\Transformer\Contracts;

/**
 * Base transformer interface contract.
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
     * Set the model to transform.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection $model
     * @return $this
     */
    public function item($model);

    /**
     * Set the sub-transformation property.
     *
     * @param bool $childTransformation
     * @return $this
     */
    public function childTransformation(bool $childTransformation);


}