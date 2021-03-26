<?php 
namespace App\Services\Admin;

use App\Services\Base as BaseService;
use App\Models\Admin\Logger;

class LogService extends BaseService
{	
	protected static $constantMap = [
        'base' => Logger::class,
    ];

	public function __construct(Logger $model)
    {
        $this->baseModel =  $model;
    }

    public function addLog(array $data)
    {
    	if (empty($data)) return false;
    	$temp = [
            'ip' => getIp(),
            'browser' => getBrowser(),
            'system' => getSystem(),
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'create_at' => $this->getTime(),
        ];
    	$data = array_merge($temp, $data);
    	return $this->baseModel->insert($data);
    }
}