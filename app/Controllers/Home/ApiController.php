<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;

class ApiController extends Controller
{
	public function stat()
	{
		$url = ipost('url');
		if (empty($url)) {
			$this->error('参数不正确');
		}
		$url = parse_url($url);
		$data = [
			'path' => trim($url['path'] ?? '', '/'),
			'query' => $url['query'] ?? '',
		];
		make('App\Services\LoggerService')->addLog($data);
		$this->success();
	}
}