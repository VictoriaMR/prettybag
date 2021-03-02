<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	属性类
 */
class ProductAttrvalueService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRIBUTE_CACHE';

	public function create(array $data)
	{
		if (empty($data['name'])) {
			return false;
		}
        $data['name'] = trim($data['name']);
        $insert = [
            'name' => $data['name'],
            'sort' => $data['sort'] ?? 0,
        ];
        $attrId = make('App\Models\ProductAttribute')->create($insert);
        //设置多语言
        $attrLanModel = make('App\Models\ProductAttributeLanguage');
        $translateService = make('App\Services\TranslateService');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['lan_id'] == env('DEFAULT_LANGUAGE_ID')) continue;
            $data = [
            	'cate_id' => $attrId,
            	'lan_id' => $value['lan_id'],
            	'name' => $translateService->getTranslate($data['name'], $value['code']),
            ];
            $attrLanModel->create($data);
        }
        return $attrId;
	}

    protected function getCacheKey()
    {
        return self::CACHE_KEY;
    }

    public function getInfo($paId = null)
    {
        $info = make('App\Models\ProductAttribute')->getInfo('pa_id, name');
        if (!empty($info)) {
            $info = array_column($info, null, 'pa_id'); 
        }
        if (empty($paId)) {
            return $info;
        }
        return $info[$paId] ?? [];
    }

    public function getInfoCache($paId = null)
    {
        $info = redis()->get($this->getCacheKey());
        if (empty($info)) {
            $info = $this->getInfo();
            redis()->set($this->getCacheKey(), $info, -1);
        }
        if (empty($paId)) {
            return $info;
        }
        return $info[$paId] ?? '';
    }

    public function deleteCache()
    {
        return redis()->delete($this->getCacheKey());
    }
}