<?php
final class Router
{
	public static $_route = []; //路由

	public static function analyze()
	{
		$pathInfo = trim($_SERVER['REQUEST_URI'], DS);
		if (empty($pathInfo)) {
			self::$_route = [
				'class' => ucfirst(APP_TEMPLATE_TYPE),
				'path' => 'Index',
				'func' => 'index',
			];
		} else {
			$pathInfo = explode(DS, explode('?', $pathInfo)[0]);
			if (empty($pathInfo[0])) {
				self::$_route = [
					'class' => ucfirst(APP_TEMPLATE_TYPE),
					'path' => 'Index',
					'func' => 'index',
				];
			} else {
		        self::$_route['class'] = ucfirst(APP_TEMPLATE_TYPE);
		        switch (count($pathInfo)) {
		        	case 0:
		        		self::$_route['path'] = 'Index';
			        	self::$_route['func'] = 'index';
		        		break;
		        	case 1:
		        		self::$_route['path'] = ucfirst(implode(DS, $pathInfo));
			        	self::$_route['func'] = 'index';
		        		break;
		        	default:
		        		$func = array_pop($pathInfo);
		        		self::$_route['path'] = ucfirst(implode(DS, $pathInfo));
		        		self::$_route['func'] = lcfirst($func);
		        		break;
		        }
			}
		}
		if (!in_array(self::$_route['class'], config('router'))) {
			throw new \Exception(self::$_route['class'] . ' was a illegal routing', 1);
		}
		return true;
	}

	public static function buildUrl($url = '', $param = [])
	{
		if (empty($url)) {
			$url = lcfirst(self::$_route['path']) . DS . lcfirst(self::$_route['func']);
		}
		if (!empty($param)) {
			$url .= '?' . http_build_query($param);
		}
		return APP_DOMAIN.$url;
	}
}