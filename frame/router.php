<?php
final class Router
{
	public static $_route = []; //路由

	public static function analyze()
	{
		$pathInfo = trim($_SERVER['REQUEST_URI'], DS);
		self::$_route['class'] = ucfirst(APP_TEMPLATE_TYPE);
		if (empty($pathInfo)) {
			self::$_route['path'] = 'Index';
			self::$_route['func'] = 'index';
		} else {
			$pathInfo = explode(DS, explode('?', $pathInfo)[0]);
			if (empty($pathInfo[0])) {
				self::$_route['path'] = 'Index';
				self::$_route['func'] = 'index';
			} else {
				if (APP_TEMPLATE_TYPE == 'home' && strpos($pathInfo[0], '.html') !== false) {
					//设置默认语言
					if (empty(\frame\Session::get('home_language_id'))) {
						\frame\Session::set('home_language_id', 1);
					}
					$pathInfo = explode('-', rtrim($pathInfo[0], '.html'));
					$temp = array_pop($pathInfo);
					//是页码
					if (strlen($temp) > 1 && strpos($temp, 'p') === 0) {
						$_GET['page'] = (int)substr($temp, 1);
						$temp = array_pop($pathInfo);
					}
					$id = (int) $temp;
					$temp = array_pop($pathInfo);
					switch ($temp) {
						case 'c':
							self::$_route['path'] = 'Category';
							$_GET['cid'] = $id;
							break;
						case 'p':
						case 'k':
							self::$_route['path'] = 'Product';
							$_GET['s'.$temp.'u_id'] = $id;
							break;
						default:
							self::$_route['path'] = 'Index';
							break;
					}
					self::$_route['func'] = 'index';
				} else {
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
		}
		if (!in_array(self::$_route['class'], config('router'))) {
			throw new \Exception(self::$_route['class'] ?? 'no class' . ' was a illegal routing', 1);
		}
		if (count(self::$_route) != 3) {
			throw new \Exception(' router analyed error', 1);
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