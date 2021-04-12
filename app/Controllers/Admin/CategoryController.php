<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class CategoryController extends Controller
{
	public function __construct()
	{
        $arr = [
            'index' => '分类列表',
        ];
		$this->_nav = array_merge(['default' => '分类管理'], $arr);
		$this->_init();
	}

	public function index()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCateInfo', 'getCateLanguage', 'editInfo', 'editLanguage'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		Html::addJs();
		$list = make('App\Services\CategoryService')->getListFormat();
		//语言列表
		$language = make('App\Services\LanguageService')->getInfo();

		$this->assign('language', $language);
		$this->assign('list', $list);
		return view();
	}

	protected function getCateInfo()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('App\Services\CategoryService')->getInfo($cateId);
		$this->success($info, '');
	}

	protected function getCateLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$info = make('App\Services\CategoryService')->getLanguage($cateId);
		$this->success($info, '');
	}

	protected function editLanguage()
	{
		$cateId = (int) ipost('cate_id');
		if (empty($cateId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('App\Services\CategoryService');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($cateId, $key, $value);
			}
		}
		$this->success('操作成功');
	}

	protected function editInfo()
	{
		$cateId = (int) ipost('cate_id');
		$name = trim(ipost('name'));
		$avatar = trim(ipost('avatar'));
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		$data = [
			'name' => $name,
			'avatar' => $avatar,
		];
		if (empty($cateId)) {
			$result = make('App\Services\CategoryService')->create($data);
		} else {
			$result = make('App\Services\CategoryService')->updateDataById($cateId, $data);
		}
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}
}