<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;
use frame\Session;

class LoginController extends Controller
{
	public function index()
	{	
		Html::addCss();
		Html::addJs();
		Session::set('admin', []);
		return view();
	}

	public function loginCode()
	{
		$imageService = make('App/Services/Common/ImageService');
		$service = make('App/Services/Base');
		$code = $service->getSalt();
		Session::set('admin_login_code', $code);
		$imageService->verifyCode($code);
	    exit();
	}

	public function login() 
	{
		$phone = ipost('phone', '');
		$code = ipost('code', '');
		$password = ipost('password', '');

		if (empty($phone) || empty($code) || empty($password)) {
			return $this->result(10000, [], ['message' => '输入错误!']);
		}
		if (strtolower($code) != strtolower(Session::get('admin_login_code'))) {
			return $this->result(10000, [], ['message' => '验证码错误!']);
		}
		$memberService = make('App/Services/Admin/MemberService');
		$result = $memberService->login($phone, $password, $memberService::constant('TYPE_MEMBER_ADMIN'));

		if ($result) {

	        $logService = \App::make('App\Services\Logger\AdminLogService');
			$data = [
	            'mem_id' => Session::get('admin_mem_id'),
	            'remark' => '登录管理后台',
	            'type_id' => $logService::constant('TYPE_ID_LOGIN'),
	        ];
	        $logService->addLog($data);
			$this->result(200, ['url' => url('index')], ['message' => '登录成功!']);
		} else {
			$this->result(10000, $result, ['message' => '账号或者密码不匹配!']);
		}
	}
}