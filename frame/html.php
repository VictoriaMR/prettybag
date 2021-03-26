<?php 

namespace frame;

class Html
{
    public static $_CSS = [];
    public static $_JS = [];

    public static function addCss($name = '')
    {
        $matchPath = '';
        if (env('APP_VIEW_MATCH')) {
            $matchPath = (isMobile() ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = \Router::$_route;
            $name = $matchPath . lcfirst($_route['path']) . DS . $_route['func'];
        }
        self::$_CSS[] = env('APP_DOMAIN') . 'css' . DS . $name . '.css';
        return true;
    }

    public static function addJs($name = '', $public = false)
    {
        $matchPath = '';
        if (env('APP_VIEW_MATCH')) {
            $matchPath = (isMobile() ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = \Router::$_route;
            $name = $matchPath . lcfirst($_route['path']) . DS . $_route['func'];
        }
        self::$_JS[] = env('APP_DOMAIN') . 'js' . DS . $name . '.js';
        return true;
    }

    public static function getCss()
    {
        return self::$_CSS;
    }

    public static function getJs()
    {
        return self::$_JS;
    }
}