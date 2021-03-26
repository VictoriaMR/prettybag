<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use frame\Html;
use frame\Session;

class IndexController extends Controller
{
	public function __construct()
	{
		$this->_nav[] = '概览';
	}

	public function index()
	{	
		Html::addCss();
		Html::addJs();
		
		$this->assign('info', Session::get('admin'));
		$this->assign('_title', '首页');
		return view();
	}

	public function statInfo()
	{
        Html::addJs('echarts');
        //系统信息
		$systemInfo = $this->getSystemInfo();
        $logService = make('App/Services/LoggerService');
        //浏览设备统计
        $viewAgentInfo = $logService->getStats('browser');
        //每日浏览人数统计
        $viewerInfo = $logService->getIpDateStat();

		$this->_nav[] = '全部概览';
		$this->assign('_title', '统计信息');
		$this->assign('_nav', $this->_nav);
        $this->assign('systemInfo', $systemInfo);
        $this->assign('viewAgentInfo', $viewAgentInfo);
        $this->assign('viewerInfo', $viewerInfo);

		return view();
	}

	protected function getSystemInfo()
    {
    	if (isWin()) {
    		$returnData = $this->sys_windows();
    	} else {
    		$returnData = $this->sys_linux();
    	}
        $returnData['system_os'] = php_uname('s').php_uname('r');//获取系统类型
        $returnData['server_software'] = $_SERVER["SERVER_SOFTWARE"];//服务器版本
        $returnData['php_version'] = PHP_VERSION; //PHP版本
        $returnData['server_addr'] = $_SERVER['SERVER_ADDR'] ?? '0.0.0.0'; //服务器IP地址
        $returnData['server_name'] = $_SERVER['SERVER_NAME']; //服务器域名
        $returnData['server_port'] = $_SERVER['SERVER_PORT']; //服务器端口

        $returnData['php_sapi_name'] = php_sapi_name(); //PHP运行方式
        $returnData['mysql_version'] = mysqlVersion(); //mysql 版本
        $returnData['max_execution_time'] = get_cfg_var('max_execution_time') . 's'; //最大执行时间
        $returnData['upload_max_filesize'] = get_cfg_var('upload_max_filesize'); //最大上传限制
        $returnData['memory_limit'] = get_cfg_var('memory_limit'); //最大内存限制
        $returnData['processor_identifier'] = $_SERVER['PROCESSOR_IDENTIFIER'] ?? ''; //服务器cpu 数
        $returnData['disk_used_rate'] = sprintf('%.2f', 1 - disk_free_space('/') / disk_total_space('/')) * 100 . '%'; //磁盘使用情况
        $returnData['disk_free_space'] = sprintf('%.2f', disk_free_space('/') / 1024 / 1024); 

        return $returnData;
    }

    protected function sys_windows()
    {
    	$out = [];
    	$data = [];
    	//cmd Cpu 使用
    	$cmd = 'wmic cpu get loadpercentage';
    	exec($cmd, $out);
    	$data['loadpercentage'] = ($out[1] ?? 0).'%';
    	//内存总量
    	$out = [];
    	$cmd = 'wmic ComputerSystem get TotalPhysicalMemory';
    	exec($cmd, $out);
    	$data['memory_total'] = sprintf('%.2f', ($out[1] ?? 0) / 1024 / 1024);
    	$out = [];
    	$cmd = 'wmic OS get FreePhysicalMemory';
    	exec($cmd, $out);
    	$data['memory_used'] = sprintf('%.2f', ($out[1] ?? 0) / 1024);
    	$data['memory_free'] = sprintf('%.2f', ($data['memory_total'] - $data['memory_used']));
    	$data['memory_free_rate'] = sprintf('%.2f', $data['memory_free'] / $data['memory_total'] * 100).'%';
    	return $data;
	}

    protected function sys_linux()
    {
        $str = shell_exec('free');
        $str = preg_replace('/\s(?=\s)/', '\\1', explode('Mem:', $str)[1]);
        $data = explode('Swap:', $str);
        $memData = explode(' ', trim($data[0]));
        $swapData = explode(' ', trim($data[1]));
        $data = [];
        $data['memory_total'] = sprintf('%.2f', ($memData[0] + ($swapData[0] ?? 0)) / 1024);
        $data['memory_used'] = sprintf('%.2f', ($memData[1] + ($swapData[1] ?? 0)) / 1024);
		$data['memory_free_rate'] = sprintf('%.2f', ($data['memory_total'] - $data['memory_used']) / $data['memory_total'] * 100).'%';
		$data['loadpercentage'] = sys_getloadavg()[0] ?? 0;
    }
}