<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductAttribute extends BaseModel
{
    //表名
    protected $_table = 'product_attribute';
    //主键
    protected $_primaryKey = 'pa_id';

    public function getInfo($fields)
    {
        return $this->loadData(null, $fields);
    }

    public function create(array $data) 
    {
    	return $this->insertGetId($data);
    }
}