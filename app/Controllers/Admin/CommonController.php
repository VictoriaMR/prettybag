<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class CommonController extends Controller
{
	public function index()
	{	
		dd('error');
	}

	public function getCrawerData()
	{
		$this->success(['version' => '1.0.0']);
	}
}