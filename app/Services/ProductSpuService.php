<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	产品类
 */
class ProductSpuService extends BaseService
{
	const CACHE_INFO_KEY = 'SPU_CACHE_INFO_';

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

	public function getInfoCache($spuId)
	{
		$spuId = (int)$spuId;
		if ($spuId < 1) {
			return false;
		}
		$info = redis()->get($this->getCacheKey($spuId));
		if ($info !== false) {
			return $info;
		}
		$info = $this->getInfo($spuId);
	}

	public function getInfo($spuId)
	{
		$spuId = (int)$spuId;
		if ($spuId < 1) {
			return false;
		}
		$info = make('App\Models\ProductSpu')->loadData($spuId);
		if (empty($info)) {
			return false;
		}
		$lanId = \frame\Session::get('home_language_id');
		$info['avatar'] = mediaUrl($info['avatar']);
		//价格格式化
		$languageService = make('App\Services\LanguageService');
		$priceFormat = $languageService->priceFormat($info['min_price'], $lanId);
		$info['price_format'] = $priceFormat['price'];
		$info['price_symbol'] = $priceFormat['symbol'];
		$info['url'] = filterUrl($info['name'], 'p', $spuId);
		//获取sku列表
		$skuService = make('App\Services\ProductSkuService');
		$skuList = $skuService->getListBySpuId($spuId);
		$skuIdArr = array_column($skuList, 'sku_id');
		//sku属性关联
		$skuRelationArr = $skuService->getAttributeRelation($skuIdArr);
		$attrIdArr = array_unique(array_column($skuRelationArr, 'attr_id'));
		$attrData = make('App\Services\AttributeService')->getInfo($attrIdArr, 1);
		$attvIdArr = array_unique(array_column($skuRelationArr, 'attv_id'));
		$attvData = make('App\Services\AttrvalueService')->getInfo($attvIdArr, 1);

		
		dd($skuRelationArr);
		//获取spu图片ID集
		$spuImageList = make('App\Models\ProductSpuImage')->getInfoBySpuId($spuId);
		//获取sku图片ID集
		$skuImageList = $skuService->getInfoBySkuIds($skuIdArr);
		//全部图片合集
		$attachArr = array_unique(array_filter(array_merge($spuImageList, array_column($skuImageList, 'attach_id'), array_column($skuRelationArr, 'attach_id'))));
		$attachArr = make('App\Services\AttachmentService')->getAttachmentListById($attachArr);
		$attachArr = array_column($attachArr, 'url', 'attach_id');
		foreach ($spuImageList as $value) {
			$info['image'][] = $attachArr[$value];
		}
		//处理sku
		foreach ($skuList as $key => $value) {
			$priceFormat = $languageService->priceFormat($value['price'], $lanId);
			$value['price_format'] = $priceFormat['price'];
			$value['price_symbol'] = $priceFormat['symbol'];
			$value['url'] = filterUrl($value['name'], 'k', $value['sku_id']);
			$value['image'] = [];
			foreach ($skuImageList as $k => $v) {
				if ($v['sku_id'] == $value['sku_id']) {
					$value['image'][] = $attachArr[$v['attach_id']];
				}
			}
			$skuList[$key] = $value;
		}
		//获取翻译语言
		if ($lanId != env('DEFAULT_LANGUAGE_ID')) {
			$skuIdArr[] = 0;
			$textArr = make('App\Services\ProductLanguageService')->getTextArr($spuId, $skuIdArr, $lanId);
			$textArr = array_column($textArr, 'name', 'sku_id');
			$info['name'] = empty($textArr[0]) ? $info['name'] : $textArr[0];
			foreach ($skuList as $key => $value) {
				$skuList[$key]['url'] = filterUrl($value['name'], 'k', $value['sku_id']);
				$skuList[$key]['name'] = empty($textArr[$value['sku_id']]) ? $skuList[$key]['name'] : $textArr[$value['sku_id']];
			}
		}
		dd($info);
	}

	protected function getCacheKey($spuId)
	{
		return self::CACHE_INFO_KEY.$spuId.'_'.\frame\Session::get('home_language_id');
	}
}