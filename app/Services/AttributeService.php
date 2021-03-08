<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	属性类
 */
class AttributeService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRIBUTE_CACHE';

	public function create(array $data)
	{
		if (empty($data['name'])) {
			return false;
		}
        $data['name'] = trim($data['name']);
        $translateService = make('App\Services\TranslateService');
        $name = $translateService->getTranslate($data['name']);
        $info = $this->getInfoByName($name);
        if (!empty($info)) {
            return $info['attr_id'];
        }
        $insert = [
            'name' => $name,
            'sort' => $data['sort'] ?? 0,
        ];
        $attrId = make('App\Models\Attribute')->create($insert);
        //设置多语言
        $attrLanModel = make('App\Models\AttributeLanguage');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['code'] == 'en') continue;
            if ($value['code'] == 'zh') {
                $name = $data['name'];
            } else {
                $name = $translateService->getTranslate($data['name'], $value['code']);
            }
            $insert = [
            	'attr_id' => $attrId,
            	'lan_id' => $value['lan_id'],
            	'name' => $name,
            ];
            $attrLanModel->create($insert);
        }
        return $attrId;
	}

    public function getInfoByName($name)
    {
        return make('App\Models\Attribute')->getInfoByWhere(['name' => $name]);
    }

    protected function getCacheKey()
    {
        return self::CACHE_KEY;
    }

    public function getInfo($attrId = null)
    {
        $info = make('App\Models\Attribute')->getInfo('attr_id, name');
        if (!empty($info)) {
            $info = array_column($info, null, 'attr_id');
        }
        if (empty($attrId)) {
            return $info;
        }
        return $info[$attrId] ?? [];
    }

    public function getInfoCache($attrId = null)
    {
        $info = redis()->get($this->getCacheKey());
        if (empty($info)) {
            $info = $this->getInfo();
            redis()->set($this->getCacheKey(), $info, -1);
        }
        if (empty($attrId)) {
            return $info;
        }
        return $info[$attrId] ?? '';
    }

    public function deleteCache()
    {
        return redis()->delete($this->getCacheKey());
    }
}