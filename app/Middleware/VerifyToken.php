<?php

namespace App\Middleware;

use frame\Session;

class VerifyToken
{
    protected static $except = [
        'admin/login/index' => true,
    ];

    public static function handle($request)
    {
        if (self::inExceptArray(implode(DS, $request))) {
            return true;
        }
        switch ($request['class']) {
            case 'Admin':
                $loginKey = 'admin_mem_id';
                break;
        }
        //检查登录状态
        if (!empty($loginKey) && empty(Session::get($loginKey))) {
            Session::set('admin_callback_url', $_SERVER['REQUEST_URI'].(empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING']));
            redirect(url('login'));
        }
        return true;
    }

    private static function inExceptArray($route)
    {
        return self::$except[$route] ?? false;
    }
}
