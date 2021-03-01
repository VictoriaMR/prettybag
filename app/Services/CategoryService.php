<?php 

namespace app\Services;

use App\Services\Base as BaseService;
use App\Models\Category;

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
        $insert = [
            'parent_id' => $data['parent_id'] ?? '',
            'avatar' => $data['avatar'] ?? '',
            'sort' => $data['sort'] ?? 0,
        ];
        $model = make('App\Models\Category');
        $cateId = $model->create($insert);
        $model = make('App\Models\CategoryLanguage');
        $data = [
        	'cate_id' => $cateId,
        	'lan_id' => env('DEFAULT_LANGUAGE_ID'),
        	'name' => trim($data['name']),
        ];
        return $model->create($data);
	}
}