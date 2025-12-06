<?php
class pix_db_manager
{
	public $prefix = '';
	public $db = null;
	public function __construct()
	{
		global $db;
		$this->db = $db;
	}
	public function e($str)
	{
		return addslashes($str);
	}
	public function q($str)
	{
		return $this->db->quote($str);
	}
	public function tbl($name)
	{
		return $this->prefix . $name;
	}
	public function insert(
		$table,
		$data = array(),
		$modDuplicate = false
	) {
		global
			$db,
			$pix;

		$insertId = false;
		$updateStr = '';
		if (
			$table != '' &&
			is_array($data) &&
			!empty($data)
		) {
			$insStr = 'insert into `' . $this->tbl($table) . '` ';
			$fields = '';
			$values = '';
			$vlArray = array();
			foreach ($data as $key => $value) {
				$fields .= ($fields ? ', ' : '') . '`' . $key . '`';
				$values .= ($values != '' ? ', ' : '') . '?';
				$vlArray[] = $value;

				if ($modDuplicate) {
					$updateStr .= ($updateStr != '' ? ', ' : '') . '`' . $key . '`' . '=values(`' . $key . '`)';
				}
			}
			$insStr .= '(' . $fields . ') values (' . $values . ')';
			if ($modDuplicate) {
				$insStr .= ' on duplicate key update ' . $updateStr;
			}

			$qry = $db->prepare($insStr);
			if ($qry->execute($vlArray)) {
				$insertId = $db->lastInsertId();
			}

			$this->checkQueryError($qry->errorInfo(), $insStr);
		}
		return $insertId;
	}

	public function multiInsert($table, $fields, $values, $modDuplicate = false)
	{
		if (
			$table &&
			$fields &&
			$values
		) {
			global $db;

			$insertStr = 'insert into `' . $this->tbl($table) . '` (';
			$modStr = '';
			foreach ($fields as $fld) {
				$insertStr .= "\n\t`$fld`, ";
				if ($modDuplicate) {
					$modStr .= "\n\t`$fld` = values(`$fld`), ";
				}
			}
			$insertStr = substr($insertStr, 0, -2) . "\n) values ";
			if ($modDuplicate) {
				$modStr = substr($modStr, 0, -2);
			}

			foreach ($values as $val) {
				$quotedValues = [];
				foreach ($val as $v) {
					if ($v === null) {
						$quotedValues[] = 'NULL';
					} else {
						$quotedValues[] = $db->quote($v);
					}
				}

				$insertStr .= "\n(" . implode(', ', $quotedValues) . '), ';
			}
			$insertStr = substr($insertStr, 0, -2);

			if ($modDuplicate) {
				$insertStr .= "\n on duplicate key update $modStr";
			}

			$this->run($insertStr);
		}
	}
	public function getInsertId()
	{
		global $db;
		return $db->lastInsertId();
	}
	public function update(
		$table,
		$conditions = array(),
		$data = array()
	) {
		global $db, $pix;
		$return = false;
		if (
			$table != '' &&
			is_array($data) &&
			!empty($data) &&
			is_array($conditions) &&
			!empty($conditions)
		) {
			$updStr = 'update `' . $this->tbl($table) . '` set ';
			$updDatas = '';
			$preValues = array();
			foreach ($data as $key => $value) {
				$updDatas .= ($updDatas != '' ? ', ' : '') . '`' . $key . '`=?';
				$preValues[] = $value;
			}
			$updStr .= $updDatas;

			$condDatas = $this->genConds($conditions);
			$updStr .= ' where ' . $condDatas;

			$qry = $db->prepare($updStr);
			$return = $qry->execute($preValues);
			$this->checkQueryError($qry->errorInfo(), $updStr);
		}
		return $return;
	}
	public function delete($table, $conditions = array())
	{
		global $db, $pix;
		if ($table != '' && is_array($conditions)) {
			$delete_str = 'delete from `' . $this->tbl($table) . '`';
			$condValues = array();
			if (!empty($conditions)) {
				$condDatas = $this->genConds($conditions);
				$delete_str .= ' where ' . $condDatas;
			}

			$qryPrep = $db->prepare($delete_str);
			$qryPrep->execute($condValues);

			$this->checkQueryError(
				$qryPrep->errorInfo(),
				$delete_str
			);
		}
	}
	public function getRow(
		$table,
		$conditions = array(),
		$displayFields = '*'
	) {
		$conditions['single'] = 1;
		return $this->get(
			$table,
			$conditions,
			$displayFields
		);
	}
	public function getVar(
		$table,
		$conditions,
		$field,
		$fldName = null
	) {
		$conditions['single'] = 1;
		return $this->get(
			$table,
			$conditions,
			$field
		)->{$fldName ?: $field} ?? '';
	}
	public function genConds($conditions)
	{
		global $db;

		$conds = [];

		if ($conditions) {
			// custom conds array
			if ($adlQueries = $conditions['__QUERY__'] ?? null) {
				if (is_array($adlQueries)) {
					$conds = array_filter(
						array_merge(
							$conds,
							$adlQueries
						)
					);
				} elseif (gettype($adlQueries) == 'string') {
					$conds[] = $adlQueries;
				}
			}

			// custom conds string
			if ($singleCond = $conditions['#QRY'] ?? '') {
				$conds[] = $singleCond;
			}

			// unset
			unset(
				$conditions['__QUERY__'],
				$conditions['#QRY']
			);
		}

		foreach ($conditions as $key => $value) {
			if (is_array($value)) {
				$value = array_map(
					array($this, 'q'),
					$value
				);
				$key = str_replace('.', '`.`', $key);
				$lmatch = '';

				if (preg_match('/__btw$/', $key)) {
					$key = str_replace('__btw', '', $key);
					$conds[] = '`' . $key . '` BETWEEN ' . ($value[0] ?? '') . ' AND ' . ($value[1] ?? '');
					// 
				} else {
					if (preg_match('/!$/', $key)) {
						$lmatch = 'not';
						$key = str_replace('!', '', $key);
					}

					$conds[] = '`' . $key . '` ' . $lmatch . ' in (
						' . (empty($value) ? 'NULL' : implode(', ', $value)) . '
					)';
				}
				// 
			} else {
				$key = preg_replace('/\./', '`.`', $key);
				$compOpr = '=';

				preg_match_all('/(__(ne|ge*|le*))$/', $key, $fmatches);
				if (
					isset(
						$fmatches[0][0],
						$fmatches[2][0]
					)
				) {
					switch ($fmatches[2][0]) {
						case 'ne':
							$compOpr = '!=';
							break;

						case 'ge':
							$compOpr = '>=';
							break;

						case 'le':
							$compOpr = '<=';
							break;

						case 'g':
							$compOpr = '>';
							break;

						case 'l':
							$compOpr = '<';
							break;
					}

					if ($compOpr != '=') {
						$key = preg_replace('/' . $fmatches[0][0] . '$/i', '', $key);
					}
				}

				if ($value === null) {
					$conds[] = '`' . $key . '` IS ' . ($compOpr == '!=' ? 'NOT' : '') . ' NULL';
				} else {
					$conds[] = "`$key` $compOpr " . $this->q($value);
				}
			}
		}

		return implode(" and \n", $conds);
	}
	public function pullData(
		$table,
		$conditions = [],
		$displayFields = '*'
	) {
		return $this->get(
			$table,
			$conditions,
			$displayFields,
			true
		);
	}
	public function get(
		$table,
		$conditions = array(),
		$displayFields = '*',
		$isAssoc = null
	) {
		global $db;
		$r = [
			'data' => []
		];
		$singleResult = isset($conditions['single']);
		if ($singleResult) {
			unset($conditions['single']);
		}
		if (
			$table != '' &&
			is_array($conditions)
		) {
			if (
				gettype($displayFields) == 'string' &&
				preg_match('/(\[|\])/', $displayFields)
			) {
				preg_match_all('/\[.+?\]/', $displayFields, $matches);
				if (isset($matches[0])) {
					foreach ($matches[0] as $match) {
						$displayFields = str_replace($match, (preg_replace('/\./', '`.`', str_replace('=', '` as `', $match))), $displayFields);
					}
				}
				$displayFields = preg_replace('/\|/', '`, `', $displayFields);
				$displayFields = preg_replace('/(\[|\])/', '`', $displayFields);
			}
			if (
				is_string($table) &&
				preg_match('/^\[.*\]$/', $table)
			) {
				$table = preg_replace('/([a-zA-Z\_0-9]{1,})\=/', $this->prefix . '$1=', $table);
				$table = preg_replace('/[\[\]]/', '`', $table);
				$table = preg_replace('/\|/', '`, `', $table);
				$table = preg_replace('/\=/', '` `', $table);
				// 
			} elseif (is_array($table)) {
				$joinStr = '';
				$lastTbl = null;
				$tableOk = true;
				foreach ($table as $tbd) {
					if (count($tbd) > 2) {
						$joinStr .= ($joinStr ? " join \n" : '') . '`' . $this->tbl($tbd[0]) . '` `' . $tbd[1] . '`';
						if ($lastTbl) {
							$joinStr .= ' on `' . $lastTbl[1] . '`.`' . ($tbd[3] ?? $lastTbl[2]) . '`=`' . $tbd[1] . '`.`' . $tbd[2] . '`';
						}
						$lastTbl = $tbd;
					} else {
						$tableOk = false;
						trigger_error('table ' . have($tbd[0], 'unknown') . ' requires 3 parameters');
					}
				}
				$table = $joinStr;
				if (!$tableOk) {
					return false;
				}
			} else {
				$table = '`' . $this->tbl($table) . '`';
			}

			if ($displayFields != '*') {
				if (is_array($displayFields)) {
					$displayFields = '`' . implode("`,\n`", $displayFields) . '`';
				}
			}

			$selectStr = 'select ' . $displayFields . " \nfrom " . $table;
			$showQuery = false;
			$conditionStr = '';
			$groupingStr = '';
			$sortingStr = '';
			$limitStr = '';
			$selVals = array();
			if (count($conditions) > 0) {
				$qryConditions = array();

				if (isset($conditions['__SHOW_QUERY__'])) {
					$showQuery = true;
				}
				if (isset($conditions['order_by']) && $conditions['order_by'] != '') {
					$sortingStr = ' order by `' . $conditions['order_by'] . '` ' . (isset($conditions['sort']) && $conditions['sort'] == 'desc' ? 'desc' : 'asc');
				}
				if (isset($conditions['group_by']) && $conditions['group_by'] != '') {
					$groupingStr = "\n GROUP BY `" . $conditions['group_by'] . '` ';
				}
				if (isset($conditions['#GRP'])) {
					if ($conditions['#GRP'] != '') {
						$groupingStr = "\n GROUP BY " . $conditions['#GRP'];
					}
				}
				if (isset($conditions['#SRT'])) {
					if ($conditions['#SRT'] != '') {
						$sortingStr = "\n ORDER BY " . $conditions['#SRT'];
					}
				}
				if (
					isset($conditions['__limit']) &&
					is_numeric($conditions['__limit'])
				) {
					$limit = intval($conditions['__limit']);
					$limit = $limit < 1 ? 1 : $limit;
					$pageNo = 0;
					if (isset($conditions['__page']) && is_numeric($conditions['__page'])) {
						$pageNo = intval($conditions['__page']);
						$pageNo = $limit < 0 ? 0 : $pageNo;
						$r['current'] = $pageNo;
						$pageNo = $pageNo * $limit;
					}
					$limitStr = "\n LIMIT " . $pageNo . ', ' . $limit;
				}

				unset(
					$conditions['__SHOW_QUERY__'],
					$conditions['order_by'],
					$conditions['sort'],
					$conditions['group_by'],
					$conditions['#SRT'],
					$conditions['#GRP'],
					$conditions['__page'],
					$conditions['__limit']
				);

				$qryConditions = $this->genConds($conditions);
				$conditionStr .= ($qryConditions ? " \nwhere \n\t" : '') . $qryConditions;
			}
			$qryString = $selectStr .
				$conditionStr .
				$groupingStr .
				$sortingStr .
				$limitStr;

			$qryPdo = $db->prepare($qryString);
			if ($showQuery) {
				$r['query'] = $selectStr .
					$conditionStr .
					$groupingStr .
					$sortingStr .
					$limitStr;

				printCode($r['query']);
			}

			if ($qryPdo->execute()) {
				$r['data'] = $qryPdo->fetchAll(
					$isAssoc ?
						PDO::FETCH_ASSOC :
						PDO::FETCH_OBJ
				);
				if ($limitStr != '') {
					if ($groupingStr == '') {
						$pageNumCheckStr = 'select count(1) as `pages` from ' . $table . $conditionStr;
						$pageNumCheck = $db->prepare($pageNumCheckStr);
						$r['pages'] = 0;
						$r['totalRows'] = 0;
						if ($pageNumCheck->execute($selVals)) {
							$pageNumResult = $pageNumCheck->fetch(PDO::FETCH_OBJ)->pages;
							$pageLimitNum = intval(preg_replace('/(.+?), /i', '', $limitStr));
							if ($pageNumResult > 0 && $pageLimitNum > 0) {
								$r['pages'] = ceil($pageNumResult / $pageLimitNum);
								$r['totalRows'] = intval($pageNumResult);
							}
						}
					} else {
						$pageNumCheckStr = 'SELECT count(*) as `rws` 
                        FROM (
                            select 1 as `pages` from ' .
							$table .
							$conditionStr .
							$groupingStr . '
                            ) x';

						$pageNumCheck = $db->prepare($pageNumCheckStr);
						$r['pages'] = 0;
						$r['totalRows'] = 0;
						if ($pageNumCheck->execute($selVals)) {
							$results = $pageNumCheck->fetchColumn();
							$pageLimitNum = intval(preg_replace('/(.+?), /i', '', $limitStr));
							$r['pages'] = ceil($results / $pageLimitNum);
							$r['totalRows'] = intval($results);
						}
					}
				}
			} else {
				$r['error'] = $qryPdo->errorInfo();
				$this->checkQueryError($r['error'], $qryString);
				$r['error'] = isset($r['error'][2]) ? $r['error'][2] : '';
			}
		}
		$r = $singleResult ? (isset($r['data'][0]) ? $r['data'][0] : false) : $r;

		if (!$isAssoc && $r) {
			$r = (object)$r;
		}

		return $r;
	}
	public function getUnion($tables, $args = [])
	{
		global $db;

		$r = (object)[
			'data' => []
		];
		$uStrs = [];
		$pgnStrs = [];

		$limitStr = '';
		$sortingStr = '';
		if ($limit = max(1, intval($args['__limit'] ?? 0))) {
			$pageNo = max(0, intval($args['__page'] ?? 0));
			$r->current = $pageNo;
			$pageNo = $pageNo * $limit;
			$limitStr = "\n LIMIT " . $pageNo . ', ' . $limit;
		}

		$tables = array_filter($tables);
		foreach ($tables as $tbl) {
			$table = $this->tbl($tbl[0]);
			$selectStr = 'SELECT ' . $tbl[2] . " \nFROM " . $table;
			$conditionStr = $this->genConds($tbl[1]);
			if ($conditionStr) {
				$conditionStr = " \nWHERE \n\t" . $conditionStr;
			}
			$qryString = $selectStr .
				$conditionStr;

			if ($limitStr) {
				$pgnStr = 'select count(1) as `pages` from ' .
					$table . $conditionStr;
				$pgnStrs[] = $pgnStr;
			}

			$uStrs[] = $qryString;
		}

		if ($sortTxt = ($args['#SRT'])) {
			$sortingStr = "\nORDER BY $sortTxt";
		}

		$uQuery = implode("\nUNION\n", $uStrs) .
			$sortingStr .
			$limitStr;

		if ($args['#PRINT'] ?? 0) {
			echo "<pre style=\"
				background-color: #333;
				color: #ddd;
				border-radius: 10px;
				padding: 30px;
			\">$uQuery</pre>";
		}

		$qryPdo = $db->prepare($uQuery);
		if ($qryPdo->execute()) {
			$r->data = $qryPdo->fetchAll(PDO::FETCH_OBJ);

			if ($pgnStrs) {
				$r->pages = 0;
				$r->totalRows = 0;

				$pgnQuery = "SELECT SUM(`pages`) as `rws` FROM \n(" .
					implode("\nUNION\n", $pgnStrs) . "\n) as pgt";
				$pageNumCheck = $db->prepare($pgnQuery);
				if ($pageNumCheck->execute()) {
					$results = $pageNumCheck->fetchColumn();
					$r->pages = ceil($results / $limit);
					$r->totalRows = intval($results);
				}
			}
		}

		return $r;
	}
	public function fetchAssoc(
		$table,
		$conditions = array(),
		$displayFields = '*',
		$idCol = 'id',
		$isAssoc = null
	) {
		$result = $this->get(
			$table,
			$conditions,
			$displayFields,
			$isAssoc
		);
		$ixData = array();
		if ($isAssoc) {
			foreach ($result['data'] as $data) {
				$ixData[$data[$idCol]] = $data;
			}
		} else {
			foreach ($result->data as $data) {
				$ixData[$data->{$idCol}] = $data;
			}
		}
		return $ixData;
	}
	public function getCol(
		$table,
		$conditions = array(),
		$col = '*',
		$alias = null
	) {
		$result = $this->get(
			$table,
			$conditions,
			$col
		);
		$colData = array();
		foreach ($result->data as $data) {
			$colData[] = $data->{$alias ?: $col};
		}
		return $colData;
	}
	public function qry($string)
	{
		global $db;
		$data = $db->prepare($string);
		$data->execute();
		$this->checkQueryError(
			$data->errorInfo(),
			$string
		);
		return $data->fetch(PDO::FETCH_OBJ);
	}
	public function custom_query($query_string)
	{
		global $db;
		$data = $db->prepare($query_string);
		$er = $data->execute();
		$response = new stdClass();
		$response->data = array();
		$response->data = $data->fetchAll(PDO::FETCH_OBJ);
		if ($er == false) {
			$response->error = $data->errorInfo();
			$response->error = isset($response->error[2]) ? $response->error[2] : '';
		}
		$this->checkQueryError(
			$data->errorInfo(),
			$query_string
		);
		return $response;
	}
	public function checkQueryError($error, $qry)
	{
		global $pix;
		if (isset($error[2])) {

			trigger_error(
				'<strong>
                    MYSQL ERROR:
                </strong>
                <div style="
                    color: #ffa000;
                    font-style: italic;
                    margin: 14px 0 32px;
                ">
                    ' . $error[2] . '
                </div>
                <div style="
                    background-color: #4f4f4f;
                    border-left: 3px solid #40a6d9;
                    color: #fff;
                    font-size: 17px;
                    margin: 20px 0 40px;
                    padding: 14px 30px;
                    white-space: pre-wrap;
                    line-height: 2em;
                ">' .
					$qry . '
                </div>'
			);
		}
	}
	public function run($str)
	{
		global $db;
		if ($db->query($str)) {
			return true;
		} else {
			$this->checkQueryError(
				$db->errorInfo(),
				$str
			);
			return false;
		}
	}
	public function fetch($str)
	{
		global $db;
		$qry = $db->query($str);
		if ($qry) {
			return $qry->fetch(PDO::FETCH_OBJ);
		} else {
			$this->checkQueryError($db->errorInfo(), $str);
			return false;
		}
	}
	public function fetchAll($str, $type = null)
	{
		global $db;
		$qry = $db->query($str);
		if ($qry) {
			return $qry->fetchAll($type ?: PDO::FETCH_OBJ);
		} else {
			$this->checkQueryError($db->errorInfo(), $str);
			return array();
		}
	}
	public function fetchCol($str)
	{
		global $db;
		$qry = $db->query($str);
		if ($qry) {
			return $qry->fetchAll(PDO::FETCH_COLUMN);
		} else {
			$this->checkQueryError($db->errorInfo(), $str);
			return array();
		}
	}
	public function prepareInsert()
	{
		global $db;
		$args = func_get_args();
		$argCounts = count($args);
		if ($argCounts > 1) {
			return $db->prepare(
				'insert into `' . $this->tbl($args[0]) . '` 
					(
						`' . implode('`, `', array_slice($args, 1)) . '`
					) 
					values (' .
					implode(
						', ',
						array_fill(0, $argCounts - 1, '?')
					) . ')'
			);
		}
		return false;
	}
}
$pix_db = new pix_db_manager();
$pixdb = $pix_db;
