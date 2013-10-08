<?php
class IndexModel extends AbstractModel {
	private $db;
	private $dbAlias		= 'lodatabase';

	private $tb_order		= 'order';
	private $tb_object 	= 'object';
	
	
	private $cols_order	= array('id', 'name', 'phone_number', 'create_date', 'from_address', 'from_left', 'from_floor', 'from_fee',
												'to_address', 'to_fee', 'to_left', 'to_floor', 'time', 'distance', 'person', 'box',
												'is_precious', 'is_pack', 'is_dismount', 'precious_name');
	private $cols_object	= array('id', 'order_id', 'name', 'num', 'create_date', 'size', 'weight', 'type');
	
	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}
	public function getOrders($params = array(), $orderby = 'create_date  desc', $limit = '') {
		$rs = $this->db->select($this->tb_order	, $this->cols_order, $params, $orderby, $limit);
	 	return $rs;
	}
	public function countOrders($params = array()){
		$rs = $this->db->count($this->tb_order	, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return false;
	}
	public function addOrder($params = array()) {
		$rs = $this->db->insert($this->tb_order, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	public function updateOrder($params = array(), $columns){
		$rs = $this->db->update($this->tb_order, $columns, $params);
		return $rs;
	}
	public function addObjects($params = array()) {
		$rs = $this->db->inserts($this->tb_object, $params);
		return $rs;
	}
	public function getObjects($params = array(), $orderby = 'create_date  desc', $limit = '') {
		$rs = $this->db->select($this->tb_object	, $this->cols_object	, $params, $orderby, $limit);
	 	return $rs;
	}
}