<?php 

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 	分类类
 */
class CategoryService extends BaseService
{
	const CACHE_KEY = 'CATEGORY_CACHE';

	public function create(array $data)
	{
		if (empty($data['name'])) {
			return false;
		}
        $data['name'] = trim($data['name']);
        $insert = [
            'parent_id' => $data['parent_id'] ?? '',
            'name' => $data['name'],
            'avatar' => $data['avatar'] ?? '',
            'sort' => $data['sort'] ?? 0,
        ];
        $cateId = make('App\Models\Category')->create($insert);
        //设置多语言
        $cateLanModel = make('App\Models\CategoryLanguage');
        $translateService = make('App\Services\TranslateService');
        $lanList = make('App\Services\LanguageService')->getInfoCache();
        foreach ($lanList as $key => $value) {
            if ($value['lan_id'] == env('DEFAULT_LANGUAGE_ID')) continue;
            $data = [
            	'cate_id' => $cateId,
            	'lan_id' => $value['lan_id'],
            	'name' => $translateService->getTranslate($data['name'], $value['code']),
            ];
            $cateLanModel->create($data);
        }
        return $cateId;
	}
}