<?php

namespace App\Services;

use App\Services\Base as BaseService;

/**
 * 翻译接口类
 */
class TranslateService extends BaseService
{
	private $http_url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

	public function getTranslate($text, $to = 'en', $from = 'zh')
	{
		if (empty(env('BAIDU_APPID')) || empty(env('BAIDU_SECRET_KEY'))) {
			return false;
		}
		$salt = time();
		$data = [
			'q' => $text,
			'from' => $from,
			'to' => $to,
			'appid' => env('BAIDU_APPID'),
			'salt' => $salt,
			'sign' => md5(env('BAIDU_APPID').$text.$salt.env('BAIDU_SECRET_KEY')),
		];
		$request = $http_url.'?'.http_build_query($data);
		for ($i = 0; $i < 5; $i ++) {
			$translateStr = \frame\Http::get($request);
			if ($translateStr !== false) {
				$translateStr = json_decode($translateStr, true);
				if (!empty($translateStr['trans_result'])) {
					return trim($translateStr['trans_result']['dst']);
				}
			}
		}
		return '';
	}
}