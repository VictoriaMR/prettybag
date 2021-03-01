<?php

namespace App\Console;

class Kernal 
{
    const COMMON_LIST = [
        ['App/Services/MemberService', 'test', '1'],
    ];

    public function run()
    {
        if (empty(self::COMMON_LIST)) return false;

        if (!empty($_SERVER['argv'][1])) {
            call_user_func_array([make($_SERVER['argv'][1]), $_SERVER['argv'][2]], []);
            exit();
        }
        \App::log();
        $minute = date('i', time());
        foreach (self::COMMON_LIST as $value) {
            if ($this->matchTime($minute, $value[2])) {
                $this->execCommand('php '.ROOT_PATH.'artisan '.$value[0].' '.$value[1]);
            }
        }
        return true;
    }

    private function execCommand($cmd)
    {
        if (strpos(php_uname(), 'Windows') === false) {
            exec($cmd);
        } else {
            pclose(popen('start /B '.$cmd, 'r')); 
        }
    }

    private function matchTime($nowTime, $setTime)
    {
        return $nowTime % $setTime == 0;
    }
}
