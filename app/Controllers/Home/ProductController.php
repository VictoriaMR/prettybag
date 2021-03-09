<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use frame\Html;

class ProductController extends Controller
{
	public function index()
	{	
		$spuId = iget('spu_id');
		$skuId = iget('sku_id');
		if (!empty($spuId)) {
			$spuInfo = make('App\Services\ProductSpuService')->getInfoCache($spuId);
			dd($spuInfo);
		}
	}
}