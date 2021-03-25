<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;
use frame\Session;

class IndexController extends Controller
{
	public function index()
	{	
		Html::addCss();
		Html::addJs();
		
		$this->assign('info', Session::get('admin'));
		$this->assign('_title', '首页');
		return view();
	}

	public function statInfo()
	{
		$this->assign('_title', '统计信息');
		return view();
	}
}