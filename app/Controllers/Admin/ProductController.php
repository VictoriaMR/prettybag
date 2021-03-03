<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class ProductController extends Controller
{
	public function index()
	{	
		dd('error');
	}

	public function create()
	{
		$data = ipost();
		// echo json_encode($data);exit();
		if (empty($data['form_crawer'])) {
			$this->error('no data to create!');
		}

		$spuDataService = make('App\Services\ProductSpuDataService');
		$siteId = $this->getSiteId($data['form_page']['bc_site_id']);
		if ($spuDataService->isExist($siteId, $data['form_crawer']['item_id'])) {
			$this->error('product already exist!');
		}
		//生成spu
		//第一张作主图
		$fileService = make('App\Services\FileService');
		$temp = $fileService->uploadUrlImage($this->filterUrl($data['form_crawer']['pdt_picture'][0]), 'product');

		$insertsku = [
			'cate_id' =>  
		];
		dd($temp);
	}

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800'], '', $url);
	}

	protected function getSiteId($name)
	{
		$siteIdArr = [
			'1688' => 1,
			'taobao' => 2,
			'tmall' => 3
		];
		return $siteIdArr[$name] ?? 0;
	}
}