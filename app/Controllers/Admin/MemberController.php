<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;

class MemberController extends Controller
{
	public function __construct()
	{
        $arr = [
            'index' => '人员列表',
            'statInfo' => '统计信息',
        ];
        $this->_tag = $arr;
		$this->_nav = array_merge(['default' => '管理人员'], $arr);
		$this->_init();
	}

	public function index()
	{
		Html::addJs();

		$status = (int) iget('status', -1);
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 20);
		$name = trim(iget('name'));
		$phone = trim(iget('phone'));

		$where = [];
		if ($status >= 0) {
			$where['status'] = $status;
		}
		if (!empty($name)) {
			$where['name,nickname'] = ['like', '%'.$name.'%'];
		}
		if (!empty($phone)) {
			$where['phone'] = ['like', '%'.$phone.'%'];
		}

		$memberService = make('App/Services/Admin/MemberService');
		$total = $memberService->getTotal($where);
		if ($total > 0) {
			$list = $memberService->getList($where, $page, $size);
		}

		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('status', $status);
		$this->assign('name', $name);
		$this->assign('phone', $phone);

		return view();
	}
}