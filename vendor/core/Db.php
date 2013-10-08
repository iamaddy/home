<?php
/**
 * Project:     Library classes
 * File:        DB.php
 *
 * 获取DB资源   : $db = DB::Instance($alias);
 * 获取PDO对象: $db = DB::PDO($alias);
 *
 * $res = $db->insert($table, $column); //插入单条记录
 * $res = $db->insertMulti($table, $columnMulti); //多条记录一次插入
 *
 * $res = $db->delete($table, $where); //删除记录
 *
 * $res = $db->update($table, $set, $where); //更新记录
 *
 * $res = $db->select($table, $column, $where, $orderBy, $limit); //查询记录
 *
 */

final class Db {
	/**
	 * 单件方法实现
	 * @var instance
	 */
	private static $instance;

	private static $config;

	private $alias;

	/**
	 * 连接资源
	 * @var db
	 */
	private $db;

	private $debug = false;

	/**
	 * debug信息
	 * @var array
	 */
	private $debugInfo = array();

	const NONE 	= null;
	const ONE 	= 'one';
	const ALL 	= 'all';

	private function __construct($alias, $multi = false) {
		//服务器
		$_servers = self::getConfig()->get('db.'.$alias);

		$this->alias = $alias;

		//连接服务器
		if($multi) $servers = array_rand($_servers);
		else $server = $_servers;
		$dsn  = $server['dsn'];
		$user = $server['user'];
		$pass = $server['pass'];
		$opts = $server['opts'];

		try {
			$this->db = new PDO($dsn, $user, $pass, $opts);
		} catch (PDOException $e) {
			//再次尝试连接
			try {
				$this->db = new PDO($dsn, $user, $pass, $opts);
			} catch (PDOException $e) {
				//写连接失败日志
				$error = 'error:'.$e->getMessage().'|dsn:'.$dsn.'user:'.$user.'pass:'.$pass;
				Log::write($error, Log::ERROR, true, true);
			}
		}
	}

	public static function Instance($alias, $multi = false) {
		if(isset(self::$instance[$alias])) return self::$instance[$alias];

		return self::$instance[$alias] = new self($alias, $multi = false);
	}

	/**
	 * 获取配置管理器
	 * @param Controlller $controller
	 */
	public static function getConfig() {
		if(!self::$config) {
			self::setConfig(new Config());
		}

		return self::$config;
	}

	/**
	 * 设置配置管理器
	 * @param Config $config
	 */
	public static function setConfig(Config $config) {
		self::$config = $config;
	}

	/**
	 * 返回PDO对象
	 * @param unknown_type $alias
	 * @param unknown_type $multi
	 */
	public static function PDO($alias, $multi = false) {
		if(isset(self::$instance[$alias])) return self::$instance[$alias]->db;

		self::$instance[$alias] = new self($alias, $multi = false);
		return self::$instance[$alias]->db;
	}

	/**
	 * 设置是否debug模式
	 * @param boolean $boolean
	 */
	public function setDebug($boolean = true) {
		$this->debug = $boolean;
	}
	/**
	 * 打印debug信息
	 */
	public function debugInfo($trace) {
		$row = $this->debugInfo[$trace];
		$debug = '<table style="border: 1px solid #CCCCCC;border-radius: 4px 4px 4px 4px;border-spacing: 3px;width: 100%; margin-bottom: 10px;">';
		$debug .= '<tr><td width="80" align="right"><b>DB INFO：</b>&nbsp;</td><td>'.$this->alias.'</td></tr>';
		if(!isset($row['result'])) $row['result'] = 'error';
		$debug .= '<tr><td align="right"><b>SQL: </b>&nbsp;</td><td>'.$row['sql'].'</td></tr>';
		$debug .= '<tr><td align="right"><b>params: </b>&nbsp;</td><td>'.$row['params'].'</td></tr>';
		$debug .= '<tr><td align="right"><b>result: </b>&nbsp;</td><td>'.$row['result'].'</td></tr>';
		if($row['result'] != 'error')$debug .= '<tr><td align="right"><b>time: </b>&nbsp;</td><td>'.($row['end_time']-$row['start_time']).'</td></tr>';
		echo $debug .= '</table>';

	}
	/**
	 * 筛选记录
	 * @param unknown_type $table
	 * @param unknown_type $column
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @param unknown_type $limit
	 * @param unknown_type $groupBy
	 * @param unknown_type $having
	 * @return boolean
	 */
	public function select($table, $columns, $params, $orderBy = NULL, $limit = NULL, $groupBy = NULL, $having = NULL) {
		if(!$this->db) return false;

		$sql[] = 'SELECT '.$this->column($columns).' FROM `'.$table.'`';
		$where = $this->where($params);
		if($where) $sql[] = $where;

		if($groupBy) $sql[] = 'GROUP BY '.$groupBy;
		if($having)  $sql[] = 'HAVING '.$having;
		if($orderBy) $sql[] = 'ORDER BY '.$orderBy;
		if($limit)	 $sql[] = 'LIMIT '.$limit;

		$sql = implode(' ', $sql);
		
		return $this->exec($sql, $this->params($params), Db::ALL);
	}
    /**
     * 分页选择
     * 用法：$result = $db->pageselect('url_config','*',array('where'=>array('cid'=>1),'pageopt'=>10));
     *    可以再翻页时候带自定义参数 size 每页条数  param：要带的参数
     *    $result = $db->pageselect('url_config','*',array('where'=>$where,'pageopt'=>array('size'=>1,'param'=>'&fuck=me')));
     * @param string $table
     * @param unknown_type $columns
     * @param unknown_type $options
     * @return array
     */
    public function pageselect($table, $columns='*', $options=array()) {
        $params  = (isset($options['where'])) ? $options['where'] :array();//where 条件
        $orderBy = (isset($options['orderby'])) ? $options['orderby'] :null;
        $groupBy = (isset($options['groupby'])) ? $options['groupby'] :null;
        $having  = (isset($options['having'])) ? $options['having'] :null;
        $pageopt = (isset($options['pageopt'])) ? $options['pageopt'] :null; //页面分页带的参数
		// 查询总数
		$res = $this->one($table,'count(1) as count',$params,$orderBy,null,$groupBy,$having);
		$count = $res['count'];
		//如果查询总数大于0
		if($count > 0) {
			// 解析分页参数
			if( is_numeric($pageopt) ) {
				$p		=	new Page($count,intval($pageopt));
			}elseif( is_array($pageopt) ){
				if( isset($pageopt['size']) && $pageopt['size']>0 ){
					$pagesize	=	intval($pageopt['size']);
				}else{
					$pagesize	=	25;
				}
				$p	=	new Page($count,$pagesize,$pageopt['param']);
			}else{
				$pagesize	=	25;
				$p		=	new Page($count,$pagesize);
			}
			// 查询数据
			$limit	=	$p->firstRow.','.$p->listRows;
			$datas	=	$this->select($table,$columns,$params,$orderBy,$limit, $groupBy, $having);

			// 输出控制
			$output['count']		=	$count;
			$output['totalPages']	=	$p->totalPages;
			$output['totalRows']	=	$p->totalRows;
			$output['nowPage']		=	$p->nowPage;
			$output['html']			=	$p->show_page();
			$output['data']			=	$datas;
			unset($datas);
			unset($p);
			unset($count);
		}else{
			$output['count']		=	0;
			$output['totalPages']	=	0;
			$output['totalRows']	=	0;
			$output['nowPage']		=	1;
			$output['html']			=	'';
			$output['data']			=	array();
		}
		// 输出数据
		return $output;
     }
	/**
	 * 筛选记录
	 * @param string $table
	 * @param array $column
	 * @param array $params
	 * @param string $orderBy
	 * @param string $limit
	 * @param string $groupBy
	 * @param string $having
	 * @return boolean
	 */
	public function one($table, $columns, $params, $orderBy = NULL, $limit = NULL, $groupBy = NULL, $having = NULL) {
		if(!$this->db) return false;

		$sql[] = 'SELECT '.$this->column($columns).' FROM `'.$table.'`';
		$where = $this->where($params);
		if($where) $sql[] = $where;

		if($groupBy) $sql[] = 'GROUP BY '.$groupBy;
		if($having)  $sql[] = 'HAVING '.$having;
		if($orderBy) $sql[] = 'ORDER BY '.$orderBy;
		if($limit)	 $sql[] = 'LIMIT 0,1';

		$sql = implode(' ', $sql);

		return $this->exec($sql, $this->params($params), Db::ONE);
	}
	function mapColumn($ele) {
		return ':'.$ele;
	}
	function mapValues($ele) {
		return mysql_real_escape_string($ele);
	}
	/**
	 * 插入记录
	 * @param unknown_type $table
	 * @param unknown_type $column
	 */
	public function insert($table, $columns) {
		if(!$this->db) return false;

		$keys = array_keys($columns);

		//$sql = 'INSERT INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES ('.implode(', ', array_map(array($this, 'mapColumn'), $keys)).')';
		$sql = 'INSERT INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES ('.implode(', ', array_map(array($this, 'mapColumn'), $keys)).')';

		return $this->exec($sql, $columns, Db::NONE);
	}
	/**
	 * 批量插入
	 * @param unknown_type $table
	 * @param unknown_type $column
	 */
	public function inserts($table, $columns) {
		if(!$this->db) return false;

		$column = array_pop($columns);
		$keys = array_keys($column);
		$sql[] = 'INSERT INTO `'.$table.'` (`'.implode('`, `', $keys).'`) VALUES';
		array_push($columns, $column);

		$_sql = array();
		foreach($columns as $row) {
			$values = array_values($row);
			$_sql[] = '("'.implode('", "', array_map(array($this, 'mapValues'), $values)).'")';
		}
		$sql[] = implode(', ', $_sql);
		$sql = implode(' ', $sql);
		return $this->exec($sql, array(), Db::NONE);
	}
	/**
	 * 返回最后一次插入ID
	 */
	public function lastInsertId() {
		return $this->db->lastInsertId();
	}
	/**
	 * 更新记录
	 * @param unknown_type $table
	 * @param unknown_type $colums
	 * @param unknown_type $params
	 */
	public function update($table, $columns, $params) {
		if(!$this->db) return false;
		$sql[] = 'UPDATE `'.$table.'` SET ';
		foreach ($columns as $key => $value) {
			$_sql[] = $key.'="'.$this->mapValues($value).'"';
		}
		$sql[] = implode(', ', $_sql);
		$where = $this->where($params);
		if($where) $sql[] = $where;
		$sql = implode(' ', $sql);
		return $this->exec($sql, $this->params($params), Db::NONE);
	}
	/**
	 * 更新记录
	 * @param unknown_type $table
	 * @param unknown_type $colums
	 * @param unknown_type $params
	 */
	public function replace($table, $columns, $params) {
		if(!$this->db) return false;

		$sql[] = 'REPLACE `'.$table.'` SET ';
		foreach ($columns as $key => $value) {
			$_sql[] = $key.'="'.$this->mapValues($value).'"';
		}
		$sql[] = implode(', ', $_sql);
		$where = $this->where($params);
		if($where) $sql[] = $where;
		$sql = implode(' ', $sql);

		return $this->exec($sql, $this->params($params), Db::NONE);
	}
	/**
	 * 删除记录
	 * @param unknown_type $table
	 * @param unknown_type $colums
	 * @param unknown_type $params
	 */
	public function delete($table, $params) {
		if(!$this->db) return false;

		$sql[] = 'DELETE FROM `'.$table.'`';
		$where = $this->where($params);
		if($where) $sql[] = $where;
		$sql = implode(' ', $sql);

		return $this->exec($sql, $this->params($params), Db::NONE);
	}
	/**
	 * 获取数量
	 * @param unknown_type $table
	 * @param unknown_type $params
	 */
	public function count($table, $params = array(), $fetch = self::ONE) {
		if(empty($table)) return false;

		$sql[] = 'SELECT COUNT(1) AS count FROM `'.$table.'`';
		$where = $this->where($params);
		if($where) $sql[] = $where;
		$sql = implode(' ', $sql);
		
		//执行查询
		$result = $this->exec($sql, $this->params($params), $fetch);

		if($fetch == self::ONE) return isset($result['count'])? (int) $result['count']: 'false';
		else if($fetch == self::ALL) return isset($result['count'])? $result['count']: array();
		else return false;
	}
	/**
	 * 解析where条件
	 * @param array $params
	 * @return string
	 */
	private function where($params) {
		if(is_string($params)) return $params;

		$where = array();
		$pattern = '/^(.*)\s+(\=|<|<\=|\!\=|>|>\=|in|like|notlike)\s*$/i';
		$comparison = '=';
		foreach ($params as $key => $value) {
			$column 	= $key;
			$comparison = '=';
			if(preg_match($pattern, $key, $matches)) {
				$column 	= $matches[1];
				$comparison = strtoupper($matches[2]);
			}

			switch (strtoupper($comparison)) {
				case 'IN':
					$value = array_map("mysql_real_escape_string", $value);
					$where[] = $this->processWhere($column, $value, 'IN');
					break;

				case 'NOTLIKE':
					$where[] = $this->processWhere($column, $value, 'NOT LIKE');
					break;

				case 'LIKE':
				default:
					$where[] = $this->processWhere($column, $value, $comparison);
					break;
			}
		}
		return $where? 'WHERE '.implode(' AND ', $where): '';
	}
	private function process($column) {
		$return = array();
		foreach($column as $key => $value) {
			$return[] = $this->processWhere($key, $value, '=');
		}
		return implode(', ', $return);
	}
	/**
	 * 生成COLUMN子句
	 * @param unknown_type $column
	 * @param unknown_type $value
	 * @param unknown_type $comparison
	 * @return string
	 */
	private function processWhere($column, $value, $comparison) {
		if($comparison == 'IN') {
			$return = '`'.$column.'` '.$comparison.' (';
			$value = array_values($value);
			foreach ($value as $key => $val) {
				$one[] = ':'.$column.'_'.$this->comparison($comparison).'_'.$key;
			}
			return $return.implode(', ', $one).')';
		}
		if(is_array($value)) {
			$value = array_values($value);
			foreach ($value as $key => $val) {
				$one[] = '`'.$column.'` '.$comparison.' :'.$column.'_'.$this->comparison($comparison).'_'.$key;
			}
			if($one) return '('.implode(' OR ', $one).')';
			else return '';
		} else return '`'.$column.'` '.$comparison.' :'.$column.'_'.$this->comparison($comparison);
	}
	private function comparison($comparison) {

		switch (strtoupper($comparison)) {
			case '=':
				$return = 'eq';
				break;
			case '<':
				$return = 'gl';
				break;
			case '<=':
				$return = 'gleq';
				break;
			case '!=':
				$return = 'noeq';
				break;
			case '>':
				$return = 'gt';
				break;
			case '>=':
				$return = 'gteq';
				break;
			case 'IN':
				$return = 'in';
				break;
			case 'LIKE':
				$return = 'like';
				break;
			case 'NOT LIKE':
			case 'NOTLIKE':
				$return = 'notlike';
				break;
		}
		return $return;
	}
	/**
	 * 解析params条件
	 * @param array $params
	 * @return string
	 */
	private function params($params) {
		$return = array();
		$pattern = '/^(.*)\s+(\=|<|<\=|\!\=|>|>\=|in|like|notlike)\s*$/i';

		foreach($params as $key => $value) {
			$column 	= $key;
			$comparison = '=';
			if(preg_match($pattern, $key, $matches)) {
				$column 	= $matches[1];
				$comparison = strtoupper($matches[2]);
			}
			if(is_array($value)) {
				$value = array_values($value);
				foreach($value as $k => $v) {
					$return[$column.'_'.$this->comparison($comparison).'_'.$k] = $v;
				}
			} else $return[$column.'_'.$this->comparison($comparison)] = $value;
		}
		return $return;
	}
	/**
	 * 解析出column
	 * @param array $params
	 * @return array
	 */
	private function column($params) {
		if(is_string($params)) return $params;

		$column = array();
		foreach($params as $key => $value) {
			$matches = preg_split('/\sas\s/', $value, PREG_SPLIT_DELIM_CAPTURE);
			if(count($matches) == 2) $column[] = '`'.$matches[0].'` AS '.$matches[1];
			else $column[] = '`'.$matches[0].'`';
		}
		return implode(', ', $column);
	}
	/**
	 * 执行查询
	 * @param unknown_type $sql
	 * @param unknown_type $params
	 * @param unknown_type $result
	 * @param unknown_type $mode
	 */
	public function exec($sql, $params, $return = Db::NONE, $mode = PDO::FETCH_ASSOC) {
		
		if($this->debug) {
			$trace = count($this->debugInfo);
			$this->debugInfo[$trace]['sql'] =  $sql;
			$this->debugInfo[$trace]['params'] =  ucfirst(var_export($params, true));
			$this->debugInfo[$trace]['start_time'] =  Trace::time();
		}
		if(!$this->db) return false;
		//预处理SQL
		try {
			$stmt = $this->db->prepare($sql);
			if(!$stmt) {
				//出错写日志
				$params = var_export($params, true);
				$params = preg_replace('/([\s]{2,})/',"\\1", $params);
				$error = sprintf("PDO: %s|SQL: %s|params: %s", implode(',', $this->db->errorInfo()), $sql, $params);
				Log::write($error, Log::ERROR, true, true);
				return false;
			}
		} catch (PDOException $e) {
			//出错写日志
			$params = var_export($params, true);
			$params = preg_replace('/([\s]{2,})/',"\\1", $params);
			$error = sprintf("PDO: %s|SQL: %s|params: %s", implode(',', $this->db->errorInfo()), $sql, $params);
			Log::write($error, Log::ERROR, true, true);
			return false;
		}
		//执行查询
		try {
			$result = $stmt->execute($params);
			if($result === false) {
				$params = var_export($params, true);
				$params = preg_replace('/([\s]{2,})/',"\\1", $params);
				$error = sprintf("PDO: %s|SQL: %s|params: %s", implode(',', $stmt->errorInfo()), $sql, $params);
				Log::write($error, Log::ERROR, true, true);
				return false;
			}
		}
		catch (PDOException $e) {
			//查询出错写日志
			$params = var_export($params, true);
			$params = preg_replace('/([\s]{2,})/',"\\1", $params);
			$error = sprintf("PDO: %s|SQL: %s|params: %s", implode(',', $stmt->errorInfo()), $sql, $params);
			Log::write($error, Log::ERROR, true, true);
			return false;
		}
		$stmt->setFetchMode($mode);

		switch($return) {
			case self::ONE:
				$return = $stmt->fetch();
				break;
			case self::ALL:
				$return = $stmt->fetchAll();
				break;
			case self::NONE:
			default:
				$return = $result;
				break;
		}
		if($this->debug) {
			$count = count($return);
			if($count > 5) $result = 'Array('.count($return).')';
			else $result = ucfirst(var_export($return, true));

			$this->debugInfo[$trace]['result'] = $result;
			$this->debugInfo[$trace]['end_time'] = Trace::time();
			$this->debugInfo($trace);
		}
		return $return;
	}

	/**
	 * 执行查询
	 * @param unknown_type $sql
	 * @param unknown_type $params
	 * @param unknown_type $result
	 * @param unknown_type $mode
	 */
	public function query($sql, $return = Db::NONE, $params=array(), $mode = PDO::FETCH_ASSOC) {
		return $this->exec($sql, $params, $return, $mode);
	}
}