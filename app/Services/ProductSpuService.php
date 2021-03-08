<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	产品类
 */
class ProductSpuService extends BaseService
{
	public function create(array $data)
	{
		return make('App\Models\ProductSpu')->create($data);
	}

	public function addSpuImage(array $data)
	{
		return make('App\Models\ProductSpuImage')->create($data);
	}

	public function addIntroduceImage(array $data)
	{
		return make('App\Models\ProductIntroduce')->create($data);
	}
}