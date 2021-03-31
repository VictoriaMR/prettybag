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
            'loginLog' => '登录日志',
        ];
        $this->_tag = $arr;
		$this->_nav = array_merge(['default' => '管理人员'], $arr);
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modify'])) {
				$this->$opn();
			}
		}

		Html::addJs();
		$status = (int) iget('status', -1);
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 20);
		$name = trim(iget('name'));
		$phone = trim(iget('phone'));
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));

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
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);

		return view();
	}

	protected function modify()
	{
		$memId = (int) ipost('mem_id');
		$status = (int) ipost('status');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$result = make('App/Services/Admin/MemberService')->updateDataById($memId, ['status' => $status]);
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function getInfo()
	{
		$memId = (int) ipost('mem_id');
		if (empty($memId)) {
			$this->error('账户ID不能为空');
		}
		$info = make('App/Services/Admin/MemberService')->getInfo($memId);
		if (empty($info)) {
			$this->error('找不到用户数据');
		}
		unset($info['password']);
		$this->success($info);
	}

	public function loginLog()
	{
		$page = (int) iget('page', 1);
		$size = (int) iget('size', 20);
		$name = trim(iget('name'));
		$phone = trim(iget('phone'));
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));



		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('name', $name);
		$this->assign('phone', $phone);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);

		return view();
	}
}