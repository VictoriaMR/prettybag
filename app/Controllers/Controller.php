<?php

namespace App\Controllers;

class Controller 
{
	protected function result($code, $data=[], $options=[])
    {
       $data = [
            'code' => $code,
            'data' => $data,
            'message' => '',
        ];
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array_merge($data, $options), JSON_UNESCAPED_UNICODE);
        exit();
    }
}
