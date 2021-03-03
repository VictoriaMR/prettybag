<?php

namespace frame;

class Str 
{
    public static function random($len) 
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $key = '';
        for ($i=0; $i<$len; $i++) {
            $key .= $str[mt_rand(0, 32)];//生成php随机数
        }
        return $key;
    }
}