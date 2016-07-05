<?php

use EGALL\Transformer\Transformer;

class ModelTransformer extends Transformer
{

    protected $keys = ['id', 'name'];

    public function getNameAttribute()
    {
        
        return $this->model->first_name . ' ' . $this->model->last_name; 

        
    }
}