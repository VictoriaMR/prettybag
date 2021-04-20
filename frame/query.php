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
	private $_groupBy='';
	private $_orderBy='';
	private $_offset;
	private $_limit=1;

	public function __construct($connect) 
	{
		$this->_connect = $connect;
	}

	public function table($table = '')
	{
		$this->_table = $table;
		return $this;
	}

	public function where($columns, $operator = null, $value = null)
	{
		if (empty($columns)) return $this;
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

	public function whereIn($column, $value = [])
	{
		return $this->where($column, 'IN', $value);
	}

	public function orderBy($columns, $operator = null)
	{
		if (empty($columns)) return $this;
		if (is_array($columns)) {
			foreach ($columns as $key => $value) {
				if (stripos($key, ' as ') === false) {
					$this->_orderBy .= $key.' '.strtoupper($value).',';
				} else {
					$this->_orderBy .= '`'.$key.'` '.strtoupper($value).',';
				}
			}
		} else {
			$this->_orderBy .= '`'.$columns.'` '.strtoupper($operator).',';
		}
		return $this;
	}

	public function groupBy($columns)
	{
		if (empty($columns)) return $this;
		if (!is_array($columns)) {
			$columns = explode(',', $columns);
		}
		foreach ($columns as $value) {
			$value = trim($value);
			if (stripos($value, ' as ') === false) {
				$this->_groupBy .= $value.',';
			} else {
				$this->_groupBy .= '`'.$value.'`,';
			}
		}
		return $this;
	}

	public function field($columns)
	{
		if (empty($columns)) return $this;
		if (!is_array($columns)) {
			$columns = explode(',', $columns);
		}
		foreach ($columns as $value) {
			$value = trim($value);
			if (stripos($value, ' as ') === false) {
				$this->_columns .= '`'.$value.'`,';
			} else {
				$this->_columns .= $value.',';
			}
		}
        return $this;
	}

	public function page($page, $size)
	{
		if ($page >= 1) {
			$this->_offset = ($page - 1) * $size;
			$this->_limit = (int) $size;
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
		$this->_columns = 'COUNT(*) AS count';
		$this->_offset = 0;
		$this->_limit = 1;
		$result = $this->get();
		return $result[0]['count'] ?? 0;
	}

	public function insert(array $data = [])
	{
		if (empty($this->_table)) {
			throw new \Exception('MySQL Error, no found table', 1);
		}
		if (empty($data[0])) $data = [$data];
		$fields = array_keys($data[0]);
		$data = array_map(function($value){
			foreach ($value as $k => $v) {
				if (is_string($v)) {
					$value[$k] = "'".addslashes($v)."'";
				} else {
					$value[$k] = (int) $v;
				}
			}
			return implode(', ', $value);
		}, $data);
		$sql = sprintf('INSERT INTO %s (`%s`) VALUES %s', $this->_table, implode('`, `', $fields), '(' . implode('), (', $data).')');
		return $this->getQuery($sql);
	}

	public function update($data = [])
	{
		if (empty($data)) return false;
		$tempArr = [];
		foreach ($data as $key => $value) {
			$tempArr[] = "`".$key."`="."'".addslashes($value)."'";
		}
		$this->analyzeWhere();
		if (!empty($this->_whereString)){
			$sql = sprintf('UPDATE `%s` SET %s WHERE %s', $this->_table, implode(', ', $tempArr), $this->_whereString);
		} else{
			$sql = sprintf('UPDATE `%s` SET %s', $this->_table, implode(', ', $tempArr));
		}
		return $this->getQuery($sql);
	}

	public function insertGetId($data)
	{
		$result = $this->insert($data);
		if (!$result) return false;
		$result = $this->getQuery('SELECT LAST_INSERT_ID() AS last_insert_id');
		if (empty($result)) return false;
		return $result[0]['last_insert_id'] ?? false;
	}

	public function delete()
	{
		$this->analyzeWhere();
		if (!empty($this->_whereString)){
			$sql = sprintf('DELETE FROM `%s` WHERE %s', $this->_table, $this->_whereString);
		} else{
			$sql = sprintf('TRUNCATE TABLE `%s`', $this->_table);
		}
		return $this->getQuery($sql);
	}

	private function getResult()
	{
		return $this->getQuery($this->getSql());
	}

	private function getSql()
	{
		if (empty($this->_table)) {
			throw new \Exception('MySQL Error, table not exist!', 1);
		}
		$this->analyzeWhere();
		$sql = sprintf('SELECT %s FROM `%s`', empty($this->_columns) ? '*' : rtrim($this->_columns, ','), $this->_table);
		if (!empty($this->_whereString)) {
			$sql .= ' WHERE ' . $this->_whereString;
		}
		if (!empty($this->_groupBy)) {
			$sql .= ' GROUP BY ' . rtrim($this->_groupBy, ',');
		}
		if (!empty($this->_orderBy)) {
			$sql .= ' ORDER BY ' . rtrim($this->_orderBy, ',');
		}
		if (!is_null($this->_offset)) {
			$sql .= ' LIMIT ' . $this->_offset;
			$sql .= ',' . $this->_limit;
		}
		return $sql;
	}

	private function analyzeWhere()
	{
		if (empty($this->_where)) return false;
		$this->_whereString = '';
		foreach ($this->_where as $item) {
			$fields = explode(',', $item[0]);
			$operator = strtoupper($item[1]);
			$value = $item[2];
			$start = '';
			$end = '';
			if (count($fields) > 1) {
				$start = ' AND (';
				$type = ' OR';
				$end = ')';
			} else {
				$start = ' AND ';
				$type = ' AND';
			}
			$tempStr = '';
			foreach ($fields as $fk => $fv) {
				$fv = trim($fv);
				if ($operator == 'IN' || $operator == 'NOT IN') {
					$valueStr = '';
					foreach ($value as $v) {
						if (is_string($v)) {
							$valueStr .= "'".addslashes($v)."',";
						} else {
							$valueStr .= (int) $value;
							$valueStr .= ',';
						}
					}

					$tempStr .= sprintf('%s `%s` %s (%s)', $fk == 0 ? '' : $type, $fv, $operator, rtrim($valueStr, ','));
				} else {
					$value = is_string($value) ? "'".addslashes($value)."'" : (int) $value;
					$tempStr .= sprintf('%s `%s` %s %s', $fk == 0 ? '' : $type, $fv, $operator, $value);
				}
			}
			$this->_whereString .= $start.$tempStr.$end;
		}
		$this->_whereString = ltrim(trim($this->_whereString), 'AND ');
		return true;
	}

	public function getQuery($sql)
	{
		if (env('APP_DEBUG')) {
			$GLOBALS['exec_sql'][] = $sql;
		}
		$conn = \frame\Connection::getInstance($this->_connect, $this->_database);
		if ($stmt = $conn->query($sql)) {
			if (is_bool($stmt)) {
				return $stmt;
			}
			while ($row = $stmt->fetch_assoc()){
			 	$returnData[] = $row;
			}
			$stmt->free();
		} else {
			throw new \Exception($sql.' '.$conn->error, 1);
		}
		$this->clear();
		return $returnData ?? null;
	}

	private function clear()
	{
		$this->_table = '';
		$this->_columns = '';
		$this->_where = [];
		$this->_whereString = '';
		$this->_groupBy = '';
		$this->_orderBy = '';
		$this->_offset = null;
		$this->_limit = 1;
		return true;
	}

	public function start() 
    {
        return $this->getQuery('START TRANSACTION');
    }

    public function rollback()
    {
        return $this->getQuery('ROLLBACK');
    }

    public function commit()
    {
        return $this->getQuery('commit');
    }
}