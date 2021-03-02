<?php

namespace App\Models;

use App\Models\Base as BaseModel;

class ProductAttributeLanguage extends BaseModel
{
    //表名
    protected $_table = 'product_attribute_language';

    public function getInfo($paId, $lanId)
    {
        return $this->getInfoByWhere(['pa_id' => $paId, 'lan_id' => $lanId]);
    }

    public function existData($paId, $lanId) 
    {
        return $this->getCount(['pa_id' => $paId, 'lan_id' => $lanId]) > 0;
    }

    public function create(array $data) 
    {
        if (empty($data['pa_id']) || empty($data['lan_id']) || empty($data['name'])) {
            return false;
        }
        if ($this->existData($data['pa_id'], $data['lan_id'])) {
            return true;
        }
    	return $this->insertGetId($data);
    }
}