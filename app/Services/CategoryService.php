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

    public function getInfo($cateId)
    {
        $cateId = (int) $cateId;
        if (empty($cateId)) {
            return [];
        }
        $info = make('App\Models\Category')->loadData($cateId);
        if (empty($info)) {
            return [];
        }
        $info['avatar_format'] = empty($info['avatar']) ? staticUrl('image/common/noimg.png') : mediaUrl($info['avatar']);
        return $info;
    }

    public function getLanguage($cateId)
    {
        $cateId = (int) $cateId;
        if (empty($cateId)) {
            return [];
        }
        return make('App\Models\CategoryLanguage')->getInfoByWhere(['cate_id' => $cateId]);
    }

    public function getList()
    {
        return make('App\Models\Category')->orderBy('sort', 'asc')->get();
    }

    public function getListFormat()
    {
        $list = $this->getList();
        if (empty($list)) return [];
        $list = $this->listFormat($list, 0, 0);
        $returnData = [];
        $this->arrayFormat($list, $returnData);
        return $returnData;
    }

    protected function listFormat($list, $parentId=0, $lev=0) 
    {
        $returnData = [];
        foreach ($list as $value) {
            $value['level'] = $lev;
            if ($value['parent_id'] == $parentId) {
                $temp = $this->listFormat($list, $value['cate_id'], $value['level'] + 1);
                if (!empty($temp)) {
                    $value['son'] = $temp;
                }
                $returnData[] = $value;
            }
        }
        return $returnData;
    }

    protected function arrayFormat($list, &$returnData)
    {
        foreach ($list as $value) {
            $temp = $value;
            unset($temp['son']);
            $returnData[] = $temp;
            if (!empty($value['son'])) {
                $this->arrayFormat($value['son'], $returnData);
            }
        }
        return true;
    }
}