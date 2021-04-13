<?php

namespace frame;

class View 
{
    private static $_instance = null;

    protected static $data = [];

    public static function getInstance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function display($template = '', $match = true)
    {
        $template = $this->getTemplate($template, $match);
        if (is_file($template)) {
            extract(self::$data);
            include($template);
        } else {
            throw new \Exception($template.' was not exist!', 1);
        }
    }

    private function getTemplate($template, $match = true) 
    {
        $matchPath = '';
        $_route = \Router::$_route;
        if ($match) {
            if (env('APP_VIEW_MATCH')) {
                $matchPath = (APP_IS_MOBILE ? 'mobile' : 'computer') . DS;
            }
            if (empty($template)) {
                $template = implode(DS, array_map('lcfirst', $_route));
            } else {
                $template = lcfirst($_route['class']) . DS . $template;
            }
            $template = 'view' . DS . $matchPath . $template;
        }
        return ROOT_PATH . $template . '.php';
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            self::$data = array_merge(self::$data, $name);
        } else {
            self::$data[$name] = $value;
        }
        return $this;
    }

    public static function load($template = '', $match = true)
    {
        return self::getInstance()->display($template, $match);
    }
}