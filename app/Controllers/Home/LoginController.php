<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use frame\Html;

class LoginController extends Controller
{
	public function index()
	{	
		Html::addCss();
		Html::addJs();
		return view();
	}
}