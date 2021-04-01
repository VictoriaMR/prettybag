<?php

namespace App\Controllers;

class Controller 
{
    protected $_nav;
    protected $_tag;
    
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
            if (!is_array($data)) {
                $options['message'] = $data;
            } else {
                $options['message'] = 'success';
            }
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

    protected function getTime()
    {
        return date('Y-m-d H:i:s');
    }

    protected function assign($name, $value = null)
    {
        return assign($name, $value);
    }

    protected function _init()
    {
        $this->assign('_tag', $this->_tag);
        $this->assign('_nav', $this->_nav);
        $this->assign('_path', \Router::$_route['path']);
        $this->assign('_func', \Router::$_route['func']);
        $this->assign('_title', $this->_tag[\Router::$_route['func']] ?? '');
    }
}
