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

    protected function success($data=[], $options=[])
    {
        if (empty($options['message'])) {
            $options['message'] = 'success';
        }
        $this->result('200', $data, $options);
    }

    protected function error($message='')
    {
        if (empty($message)) {
            $message = 'error';
        }
        $this->result('10000', [], ['message' => $message]);
    }
}
