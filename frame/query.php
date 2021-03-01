<?php

namespace frame;

Class Query
{
	private $_connect;
	private $_database;
	private $_table;
	private $_columns;
	private $_where;
	private $_whereString;
	private $_param;
	private $_groupBy;
	private $_orderBy;
	private $_offset;
	private $_limit;

	public function __construct($connect, $table) 
	{
		$this->_connect = $connect;
		$this->_table = $table;
	}

	public function table($table = '')
	{
		$this->_table = $table;
		return $this;
	}

	public function where($columns, $operator = null, $value = null)
	{
		if (empty($columns)) return $this

		//简单处理 where条件
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (is_array($value)) {
					$this->_where[] = [$key, $value[0], $value[1]];
				} else {
					$this->_where[] = [$key, '=', $value];
				}
			}
		} else {
			if (is_null($value)) {
				$value = $operator;
				$operator = '=';
			}
			$this->_where[] = [$columns, $operator, $value];
		}
		return $this;
	}

	public function field($columns)
	{
		if (empty($columns)) return $this;
		if (is_array($columns)) {
			$this->_columns = $columns;
		} else if (count(func_get_args()) > 0) {
			$this->_columns = func_get_args();
		} else {
			$this->_columns = explode(',', $columns);
		}
        return $this;
	}

	public function get()
	{
		return $this->getResult();
	}

	public function find()
	{
		$this->_offset = 0;
		$this->_limit = 1;
		return $this->get()[0] ?? [];
	}

	public function count()
	{
		$this->_columns = ['COUNT(*) as count'];
		$this->_offset = 0;
		$this->_limit = 1;
		$result = $this->get();
		return $result[0]['count'] ?? 0;
	}

	public function insert(array $data = [])
	{
		if (empty($data)) return false;
		if (empty($data[0])) $data = [$data];

		$fields = array_keys($data[0]);
		$data = array_map(function($value){
			return "'".implode("', '", $value)."'";
		}, $data);
		$sql = sprintf('INSERT INTO %s (`%s`) VALUES %s', $this->_table, implode('`, `', $fields), '(' . implode('), (', $data).')');
		return $this->getQuery($sql);
	}

	public function insertGetId($data)
	{
		$result = $this->insert($data);
		if (!$result) return 0;
		$result = $this->getQuery('SELECT LAST_INSERT_ID() AS last_insert_id');
		if (empty($result)) return 0;
		return $result[0]['last_insert_id'] ?? 0;
	}

	private function getResult()
	{
		return $this->getQuery($this->getSql(), $this->_param);
	}

	private function getSql()
	{
		if (empty($this->_table)) {
			throw new \Exception('MySQL SELECT QUERY, table not exist!', 1);
		}
		//解析条件
		$this->analyzeWhere();
		$sql = sprintf('SELECT %s FROM `%s`', !empty($this->_columns) ? implode(', ', $this->_columns) : '*', $this->_table ?? '');
		if (!empty($this->_whereString)) {
			$sql .= ' WHERE 1=1 ' . $this->_whereString;
		}
		if (!empty($this->_groupBy)) {
			$sql .= ' GROUP BY ' . $this->_groupBy;
		}
		if (!empty($this->_orderBy)) {
			$sql .= ' ORDER BY ' . $this->_orderBy;
		}
		if ($this->_offset !== null) {
			$sql .= ' LIMIT ' . (int) $this->_offset;
		}
		if ($this->_limit !== null ) {
			$sql .= ',' . (int) $this->_limit;
		}
		return $sql;
	}

	private function analyzeWhere()
	{
		if (empty($this->_where)) return false;

		$this->_whereString = '';
		$this->_param = [];
		foreach ($this->_where as $item) {
			$fields = $item[0];
			$operator = $item[1];
			$value = $item[2];
			$operator = strtoupper($operator);
			$fields = explode(',', $fields);
			$fieldscount = count($fields);
			if ($fieldscount > 1) {
				$this->_whereString .= ' AND (';
				$type = ' OR';
			} else {
				$type = ' AND';
			}
			$tempStr = '';
			foreach ($fields as $fk => $fv) {
				$fv = trim($fv);
				switch ($operator) {
					case 'IN':
						if (!is_array($value)) $value = explode(',', $value);
						$inStr = '';
						foreach ($value as $invalue) {
							if ($inkey > 0) {
								$inStr .= ', ';
							}
							$inStr .= '?';
						}
						$tempStr .= sprintf('%s `%s` %s (%s)', $fk == 0 && $fieldscount > 1 ? '' : $type, $fv, $operator, $inStr);
						$this->_param = array_merge($this->_param, $value);
						break;
					default:
						$tempStr .= sprintf('%s `%s` %s ?', $fk == 0 && $fieldscount > 1 ? '' : $type, $fv, $operator);
						$this->_param[] = $value;
						break;
				}
			}
			$this->_whereString .= $tempStr;

			if ($fieldscount > 1) {
				$this->_whereString .= ' )';
			}
		}
		return true;
	}

	private function getQuery($sql = '', $params = [])
	{
		if (env('APP_DEBUG')) {
			$GLOBALS['exec_sql'][] = sprintf(str_replace('?', '%s', $sql), ...$params);
		}
		$conn = \frame\Connection::getInstance($this->_connect, $this->_database);
		if (!empty($params)) {
			if ($stmt = $conn->prepare($sql)) {
				//这里是引用传递参数
			    $stmt->bind_param($this->analyzeType(), ...$params);
			    $stmt->execute();
			    $result = $stmt->get_result();
		        while ($row = $result->fetch_assoc()) {
		        	$returnData[] = $row;
		        }
			    $stmt->free_result();
			    $stmt->close();
			} else {
				throw new \Exception($conn->error, 1);
			}
		} else {
			if ($stmt = $conn->query($sql)) {
				if (is_bool($stmt)) {
					$returnData =  mysqli_affected_rows($conn);
				} else {
					while ($row = $stmt->fetch_assoc()){
					 	$returnData[] = $row;
					}
					$stmt->free();
				}
			} else {
				throw new \Exception($conn->error, 1);
			}
		}
		$conn->close();
		$this->clear();
		return $returnData ?? false;
	}

	private function clear()
	{
		//清空储存数据
		$this->_columns = null;
		$this->_where = [];
		$this->_whereString = '';
		$this->_param = [];
		$this->_groupBy = '';
		$this->_orderBy = '';
		$this->_offset = null;
		$this->_limit = null;
		return true;
	}

	private function analyzeType()
	{
		$typeStr = '';
		foreach ($this->_param as $key => $value) {
			if (is_numeric($value)) $typeStr .= 'd';
			else $typeStr .= 's';
		}
		return $typeStr;
	}
}