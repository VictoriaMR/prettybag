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
            $matchPath = (ismobile() ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = \Router::$_route;
            $name = lcfirst($_route['path']) . DS . $_route['func'];
        } else {
            if (false === strrpos($name, DS)) {
                $_route = \Router::$_route;
                $name = lcfirst($_route['path']) . DS . $_route['func'];
            }
        }
        self::$_CSS[] = env('APP_DOMAIN') . 'css' . DS . $matchPath . $name . '.css';
        return true;
    }

    public static function addJs($name = '', $public = false)
    {
        $matchPath = '';
        if (env('APP_VIEW_MATCH')) {
            $matchPath = (ismobile() ? 'mobile' : 'computer') . DS;
        }
        if (empty($name)) {
            $_route = \Router::$_route;
            $name = lcfirst($_route['path']) . DS . $_route['func'];
        } else {
            if (false === strrpos($name, DS)) {
                $_route = \Router::$_route;
                $name = lcfirst($_route['path']) . DS . $_route['func'];
            }
        }
        self::$_JS[] = env('APP_DOMAIN') . 'js' . DS . $matchPath . $name . '.js';
        return true;
    }

    public static function getCss()
    {
        if (empty(self::$_CSS)) return [];
        return array_unique(self::$_CSS);
    }

    public static function getJs()
    {
        if (empty(self::$_JS)) return [];
        return array_unique(self::$_JS);
    }
}