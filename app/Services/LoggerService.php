<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\Logger;
use frame\Session;

/**
 * 	访问日志类
 */
class LoggerService extends BaseService
{
	protected static $constantMap = [
        'base' => Logger::class,
    ];

	public function __construct(Logger $model)
    {
        $this->baseModel = $model;
    }

	public function addLog()
	{
		$data = [
			'mem_id' => (int)Session::get(strtolower(\Router::$_route['class']).'_mem_id'),
			'language' => Session::get('site_language_name'),
			'is_moblie' => APP_IS_MOBILE ? 1 : 0,
			'ip' => getIp(),
			'path' => $_SERVER['REQUEST_URI'] ?? '',
			'system' => getSystem(),
			'browser' => getBrowser(),
			'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'create_at' => $this->getTime(),
		];
		return $this->baseModel->insert($data);
	}

	public function getStats($field)
	{
		return $this->baseModel->field('count(*) AS count, '.$field)->groupBy($field)->get();
	}

	public function getIpDateStat($limit = 14)
	{
		$sql = 'SELECT COUNT(*) AS count, a.`format_date` FROM (SELECT `ip`, DATE_FORMAT(`create_at`,"%Y-%m-%d") AS `format_date` FROM `visitor_log` GROUP BY `ip`,`format_date`) a GROUP BY a.`format_date` ORDER BY a.`format_date` DESC LIMIT '.$limit;
		return $this->baseModel->getQuery($sql);
	}
}