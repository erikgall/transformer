<?php

namespace EGALL\Transformer\Contracts;

use Illuminate\Support\Collection;

/**
 * CollectionTransformer interface contract.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
interface CollectionTransformer
{
    /**
     * Set the collection to transform.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return $this
     */
    public function collection(Collection $collection);

    /**
     * Set the transformation as a sub-transformation.
     *
     * @param bool $childTransformation
     * @return $this
     */
    public function childTransformation(bool $childTransformation);
}
