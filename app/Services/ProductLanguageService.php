<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\ProductLanguage;

/**
 * 	产品类
 */
class ProductLanguageService extends BaseService
{
	protected static $constantMap = [
        'base' => ProductLanguage::class,
    ];

	public function __construct(ProductLanguage $model)
    {
        $this->baseModel = $model;
    }

	public function create(array $data)
	{
		if ($this->baseModel->isExist($data['spu_id'], $data['sku_id'], $data['lan_id'])) {
			return false;
		} else {
			return $this->baseModel->create($data);
		}
	}
}