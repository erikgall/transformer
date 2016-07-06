<?php

use EGALL\Transformer\Transformer;

/**
 * MockModelTransformer Class
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class MockModelTransformer extends Transformer
{

    /**
     * The model array keys to use.
     *
     * @var array
     */
    protected $keys = ['id', 'name'];

    /**
     * Set the name attribute.
     *
     * @return string
     */
    public function getNameAttribute($item)
    {
        
        return $item->first_name . ' ' . $item->last_name;

    }
}