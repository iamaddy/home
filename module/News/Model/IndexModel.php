<?php
class IndexModel extends AbstractModel {
	private $db;
	private $dbAlias		= 'lodatabase';

	private $tb_news		= 'news';
	
	private $cols_news	= array('id', 'title', 'content', 'create_date');
	
	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}
	public function getNews($params = array(), $orderby = 'create_date  desc', $limit = '') {
		$rs = $this->db->select($this->tb_news	, $this->cols_news, $params, $orderby, $limit);
	 	return $rs;
	}
	public function countNews($params = array()){
		$rs = $this->db->count($this->tb_news	, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return false;
	}
	public function addNews($params = array()) {
		$rs = $this->db->insert($this->tb_news, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	public function updateOrder($params = array(), $columns){
		$rs = $this->db->update($this->tb_order, $columns, $params);
		return $rs;
	}
}