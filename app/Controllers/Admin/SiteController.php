<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class SiteController extends Controller
{
	public function __construct()
	{
        $arr = [
            'index' => '站点配置',
            'staticCache' => 'CSS/JS缓存',
        ];
        $this->_tag = $arr;
		$this->_nav = array_merge(['default' => '站点设置'], $arr);
		$this->_init();
	}

	public function index()
	{	
		return view();
	}

	public function staticCache()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['deleteStaticCache'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		Html::addJs();
		$files = [];
		$path = ROOT_PATH.'admin'.DS.'static';
		$this->getFileList($path, $files);
		$path = ROOT_PATH.'home'.DS.'static';
		$this->getFileList($path, $files);

		if (!empty($files)) {
			$list = [];
			foreach ($files as $key => $value) {
				$list[] = [
					'name' => str_replace(ROOT_PATH, '', $value),
					'size' => filesize($value),
					'c_time' => date('Y-m-d H:i:s', filectime($value)),
				];
			}
		}

		$this->assign('list', $list ?? []);

		return view();
	}

	protected function deleteStaticCache()
	{
		$name = ipost('name');
		if (empty($name)) {
			$this->error('名称不能为空');
		}
		if ($name == 'all') {
			$files = [];
			$path = ROOT_PATH.'admin'.DS.'static';
			$this->getFileList($path, $files);
			$path = ROOT_PATH.'home'.DS.'static';
			$this->getFileList($path, $files);
			if (!empty($files)) {
				foreach ($files as $key => $value) {
					unlink($value);
				}
			}
		} else {
			$file = ROOT_PATH.$name;
			if (!is_file($file)) {
				$this->error('非法请求, 文件不存在');
			}
			unlink($file);
		}
		$this->success('操作成功');
	}


	protected function getFileList($path, &$files)
	{
		if (is_dir($path)) {
			$dp = dir($path);
			while ($file = $dp ->read()){
	            if($file != '.' && $file != '..') {
	                $this->getFileList($path.DS.$file, $files);
	            }
	        }
		} else if (is_file($path)) {
			$files[] = $path;
		}
	}
}