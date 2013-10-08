<?php
class IndexModel extends AbstractModel {
	private $db;
	private $dbAlias		= 'addybase';

	private $tb_image 	= 'image';
	private $tb_img_comment = 'img_comment';
	private $tb_img_like	= 'img_like';
	
	private $cols_image	= array('id', 'image_id', 'title', 'shoot_time', 'image_path', 'image_name', 'create_time', 'describe');
	private $cols_img_comment = array('id', 'image_id', 'comment', 'ip', 'create_time', 'mail', 'author');
	private $cols_img_like = array('id', 'image_id', 'ip', 'create_time');
	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}
	public function getPhotos($params = array(), $orderby = 'create_time  desc', $limit = '') {
		$rs = $this->db->select($this->tb_image, $this->cols_image, $params, $orderby, $limit);
	 	return $rs;
	}
	public function addPhoto($params = array()){
		$rs = $this->db->insert($this->tb_image, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	public function countPhotos($params = array()){
		$rs = $this->db->count($this->tb_image, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return false;
	}
	public function getOnePhoto($params = array(), $orderby = 'create_time  desc', $limit = '') {
		$rs = $this->db->one($this->tb_image, $this->cols_image, $params, $orderby, $limit);
		return $rs;
	}
	
	// 图像留言
	public function getImgComments($params = array(), $orderby = 'create_time  desc', $limit = '') {
		$rs = $this->db->select($this->tb_img_comment, $this->cols_img_comment, $params, $orderby, $limit);
		return $rs;
	}
	public function addImgComment($params = array()){
		$rs = $this->db->insert($this->tb_img_comment, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	public function countImgComment($params = array()){
		$rs = $this->db->count($this->tb_img_comment, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return 0;
	}
	public function delImgComment($params = array()){
		
	}
	
	// 图像喜欢数
	public function getImgLike($params = array(), $orderby = 'create_time  desc', $limit = '') {
		$rs = $this->db->select($this->tb_img_like, $this->cols_img_like, $params, $orderby, $limit);
		return $rs;
	}
	public function addImgLike($params = array()){
		$rs = $this->db->insert($this->tb_img_like, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	public function countImgLike($params = array()){
		$rs = $this->db->count($this->tb_img_like, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return 0;
	}
}