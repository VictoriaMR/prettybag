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
		//查询产品是否入库
		$spuDataService = make('App\Services\ProductSpuDataService');
		$siteId = $this->getSiteId($data['form_page']['bc_site_id']);
		if ($spuDataService->isExist($siteId, $data['form_crawer']['item_id'])) {
			$this->error('product already exist!');
		}
		$translateService = make('App\Services\TranslateService');
		$fileService = make('App\Services\FileService');
		//Spu图片
		$spuImageArr = [];
		foreach ($data['form_crawer']['pdt_picture'] as $key => $value) {
			$url = $this->filterUrl($value);
			$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'product');
		}
		$spuImageArr = array_filter($spuImageArr);
		if (empty($spuImageArr)) {
			$this->error('product spu image empty!');
		}
		//价格合集
		$priceArr = array_column($data['form_crawer']['sku'], 'price');
		$spuNameEn = $translateService->getTranslate($data['form_crawer']['name']);
		$insert = [
			'cate_id' => (int)$data['form_page']['bc_product_category'],
			'status' => 1,
			'avatar' => $spuImageArr[0]['cate'].DS.$spuImageArr[0]['name'].'.'.$spuImageArr[0]['type'],
			'min_price' => min($priceArr) + 250,
			'name' => $spuNameEn,
			'add_time' => $this->getTime(),
		];
		$spuService = make('App\Services\ProductSpuService');
		$spuId = $spuService->create($insert);
		if (empty($spuId)) {
			$this->error('product spu create failed!');
		}
		//多语言配置 默认en
		$lanArr = make('App\Services\LanguageService')->getInfoCache();
		$productLanguageService = make('App\Services\ProductLanguageService');
		foreach ($lanArr as $key => $value) {
			if ($value['code'] == 'en') continue;
			if ($value['code'] != 'zh') {
				$name = $translateService->getTranslate($data['form_crawer']['name']);
			} else {
				$name = $data['form_crawer']['name'];
			}
			$insert = [
				'spu_id' => $spuId,
				'sku_id' => 0,
				'lan_id' => $value['lan_id'],
				'name' => $name,
			];
			$productLanguageService->create($insert);
		}
		//spu扩展数据
		$insert = [
			'spu_id' => $spuId,
			'site_id' => $this->getSiteId($data['form_page']['bc_site_id']),
			'item_id' => $data['form_crawer']['item_id'],
			'item_no' => $data['form_crawer']['item_no'],
			'item_url' => $data['form_crawer']['product_url'],
			'shop_name' => $data['form_crawer']['shop_name'],
			'shop_url' => $data['form_crawer']['shop_url'],
		];
		$spuDataService->create($insert);
		//spu图片组
		$insert = [];
		$count = 1;
		foreach ($spuImageArr as $value) {
			$insert[] = [
				'spu_id' => $spuId,
				'attach_id' => $value['attach_id'],
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			$spuService->addSpuImage($insert);
		}
		//spu 介绍图片
		$insert = [];
		$count = 1;
		foreach ($data['form_crawer']['des_picture'] as $value) {
			$url = $this->filterUrl($value);
			if (empty($spuImageArr[$url])) {
				$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'introduce', false);
			}
			if (empty($spuImageArr[$url]['attach_id'])) continue;
			$insert[] = [
				'spu_id' => $spuId,
				'attach_id' => $spuImageArr[$url]['attach_id'],
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			$spuService->addIntroduceImage($insert);
		}
		//属性组
		$attr = [];
		$attv = [];
		$attributeService = make('App\Services\AttributeService');
		$attrvalueService = make('App\Services\AttrvalueService');
		foreach ($data['form_crawer']['attr'] as $key => $value) {
			$attr[$value['attrName']] = $attributeService->create(['name' => $value['attrName']]);
			foreach ($value['attrValue'] as $k => $v) {
				$attv[$v['name']] = $attrvalueService->create(['name' => $v['name']]);
			}
		}
		print_r($attr);
		dd($attv);

		//sku
		foreach ($data['form_crawer']['sku'] as $key => $value) {
			$insert = [
				'spu_id' => $spuId,
				'status' => $value['stock'] > 0 ? 1 : 0,
				'stock' => $value['stock'],
				'price' => $value['price'],
			];
		}


		dd($lanArr);
		$spuLanguageService = make('');
		dd($insertsku);
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