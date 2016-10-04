<?php

use EGALL\Transformer\BaseTransformer;

class MockBaseTransformer extends BaseTransformer
{
    /**
     * Get the data in array format.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
