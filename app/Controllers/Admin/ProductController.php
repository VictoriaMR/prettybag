<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class ProductController extends Controller
{
	public function __construct()
	{
        $arr = [
            'index' => '产品列表',
        ];
		$this->_nav = array_merge(['default' => '产品管理'], $arr);
		$this->_init();
	}

	public function index()
	{	
		dd('error');
	}

	public function create()
	{
		$data = ipost();
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
		$firstImage = [];
		foreach ($data['form_crawer']['pdt_picture'] as $key => $value) {
			$url = $this->filterUrl($value);
			$spuImageArr[$url] = $fileService->uploadUrlImage($url, 'product');
			if ($key == 0) {
				$firstImage = $spuImageArr[$url];
			}
		}
		$spuImageArr = array_filter($spuImageArr);
		if (empty($spuImageArr)) {
			$this->error('product spu image empty!');
		}
		//价格合集
		foreach ($data['form_crawer']['sku'] as $key => $value) {
			$data['form_crawer']['sku'][$key]['price'] = $value['price'] + rand(250, 300);
		}
		$priceArr = array_column($data['form_crawer']['sku'], 'price');
		$spuNameEn = $translateService->getTranslate($data['form_crawer']['name']);
		$insert = [
			'cate_id' => (int)$data['form_page']['bc_product_category'],
			'status' => 1,
			'avatar' => $firstImage['cate'].DS.$firstImage['name'].'.'.$firstImage['type'],
			'min_price' => min($priceArr),
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
			$attr[$value['attrName']] = $attributeService->addNotExist($value['attrName']);
			foreach ($value['attrValue'] as $k => $v) {
				$attv[$v['name']] = $attrvalueService->addNotExist($v['name']);
			}
		}
		//sku
		$skuService = make('App\Services\ProductSkuService');
		foreach ($data['form_crawer']['sku'] as $key => $value) {
			$nameZhStr = $nameEnStr = '';
			//sku 属性
			foreach ($value['pvs'] as $k => $v) {
				$nameZhStr .= ' '.$v['text'];
				$nameEnStr .= ' '.$attv[$v['text']]['name'];
			}
			$insert = [
				'spu_id' => $spuId,
				'status' => $value['stock'] > 0 ? 1 : 0,
				'stock' => $value['stock'],
				'price' => $value['price'],
				'name' => $spuNameEn.' - '.trim($nameEnStr),
				'origin_price' => round($value['price'] / ((10 - rand(5, 9)) / 10), 2),
				'add_time' => $this->getTime(),
			];
			$skuId = $skuService->create($insert);
			//多语言
			foreach ($lanArr as $k => $v) {
				if ($v['code'] == 'en') continue;
				if ($v['code'] != 'zh') {
					$name = $translateService->getTranslate($data['form_crawer']['name'].' - '.trim($nameZhStr));
				} else {
					$name = $data['form_crawer']['name'].' - '.trim($nameZhStr);
				}
				$insert = [
					'spu_id' => $spuId,
					'sku_id' => $skuId,
					'lan_id' => $v['lan_id'],
					'name' => $name,
				];
				$productLanguageService->create($insert);
			}
			//sku图片
			if (!empty($value['sku_img'])) {
				if (empty($spuImageArr[$value['sku_img']])) {
					$spuImageArr[$value['sku_img']] = $fileService->uploadUrlImage($value['sku_img'], 'product');
				}
				$insert = [
					'sku_id' => $skuId,
					'attach_id' => $spuImageArr[$value['sku_img']]['attach_id'],
					'sort' => 1,
				];
				$skuService->addImage($insert);
			}
			//属性关联
			$insert = [];
			$count = 1;
			foreach ($value['pvs'] as $k => $v) {
				if (empty($v['img'])) {
					$attachId = 0;
				} else {
					if (empty($spuImageArr[$v['img']])) {
						$spuImageArr[$v['img']] = $fileService->uploadUrlImage($v['img'], 'product');
					}
					$attachId = $spuImageArr[$v['img']]['attach_id'];
				}
				$insert[] = [
					'sku_id' => $skuId,
					'attr_id' => $attr[$k]['attr_id'],
					'attv_id' => $attv[$v['text']]['attv_id'],
					'attach_id' => $attachId,
					'sort' => $count++,
				];
			}
			if (!empty($insert)) {
				$skuService->addAttributeRelation($insert);
			}
		}
		$this->success();
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