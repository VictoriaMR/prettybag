<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	属性值类
 */
class AttrvalueService extends BaseService
{
	const CACHE_KEY = 'PRODUCT_ATTRVALUE_CACHE';

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
            return $info['attv_id'];
        }
        $insert = [
            'name' => $name,
            'sort' => $data['sort'] ?? 0,
        ];
        $attvId = make('App\Models\Attrvalue')->create($insert);
        //设置多语言
        $attrLanModel = make('App\Models\AttrvalueLanguage');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['code'] == 'en') continue;
            if ($value['code'] == 'zh') {
                $name = $data['name'];
            } else {
                $name = $translateService->getTranslate($data['name'], $value['code']);
            }
            $insert = [
            	'attv_id' => $attvId,
            	'lan_id' => $value['lan_id'],
            	'name' => $name,
            ];
            $attrLanModel->create($insert);
        }
        return $attvId;
	}

    public function getInfoByName($name)
    {
        return make('App\Models\Attrvalue')->getInfoByWhere(['name' => $name]);
    }

    protected function getCacheKey()
    {
        return self::CACHE_KEY;
    }

    public function getInfo($attvId = null)
    {
        $info = make('App\Models\Attrvalue')->getInfo('attv_id, name');
        if (!empty($info)) {
            $info = array_column($info, null, 'attv_id');
        }
        if (empty($attvId)) {
            return $info;
        }
        return $info[$attvId] ?? [];
    }

    public function getInfoCache($attvId = null)
    {
        $info = redis()->get($this->getCacheKey());
        if (empty($info)) {
            $info = $this->getInfo();
            redis()->set($this->getCacheKey(), $info, -1);
        }
        if (empty($attvId)) {
            return $info;
        }
        return $info[$attvId] ?? '';
    }

    public function deleteCache()
    {
        return redis()->delete($this->getCacheKey());
    }
}