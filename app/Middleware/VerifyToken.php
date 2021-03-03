<?php

namespace App\Middleware;

use frame\Session;

class VerifyToken
{
    protected static $except = [
        'admin' => [
            'login' => true,
            'common' => true,
            'product' => true,
        ],
    ];

    public static function handle($request)
    {
        if (self::inExceptArray($request)) {
            return true;
        }
        switch ($request['class']) {
            case 'Admin':
                $loginKey = 'admin_mem_id';
                break;
        }
        //检查登录状态
        if (!empty($loginKey) && empty(Session::get($loginKey))) {
            Session::set('admin_callback_url', rtrim($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']), '?');
            redirect(url('login'));
        }
        return true;
    }

    private static function inExceptArray($route)
    {
        $class = strtolower($route['class']);
        if (empty(self::$except[$class])) {
            return true;
        }
        $path = strtolower($route['path']);
        if ((self::$except[$class][$path] ?? false)) {
            return true;
        }
        return self::$except[$class][$path.'/'.$route['func']] ?? false;
    }
}
