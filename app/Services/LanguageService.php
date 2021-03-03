<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	语言类
 */
class LanguageService extends BaseService
{	
	const CACHE_KEY = 'LANGUAGE_CACHE';

	public function create(array $data)
	{
		if (empty($data['code']) || empty($data['name'])) {
			return false;
		}
        $data = [
            'code' => $data['code'],
            'name' => $data['name'],
            'sort' => $data['sort'] ?? 0,
        ];
        return make('App\Models\Language')->create($data);
	}

    public function getInfo($code = '')
    {
    	$info = make('App\Models\Language')->getInfo('code, name');
    	if (!empty($info)) {
    		$info = array_column($info, null, 'code');
    	}
    	if (empty($code)) {
    		return $info;
    	}
    	return $info[$code] ?? [];
    }

    protected function getCacheKey()
    {
    	return self::CACHE_KEY;
    }

    public function getInfoCache($code = '')
    {
    	$info = redis()->get($this->getCacheKey());
    	if (empty($info)) {
    		$info = $this->getInfo();
    		redis()->set($this->getCacheKey(), $info, -1);
    	}
    	if (empty($code)) {
    		return $info;
    	}
    	return $info[$code] ?? '';
    }

    public function deleteCache()
    {
    	return redis()->delete($this->getCacheKey());
    }
}