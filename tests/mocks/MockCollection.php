<?php

/**
 * MockCollection Class
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class MockCollection extends Illuminate\Database\Eloquent\Collection
{

    /**
     * Takeover the load method for testing purposes.
     * 
     * @param mixed $relations
     * @return $this
     */
    public function load($relations)
    {

        $this->each(function($item) use ($relations) {

            $item->load($relations);
            
        });

        return $this;

   }
}