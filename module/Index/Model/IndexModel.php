<?php
class IndexModel extends AbstractModel {
	private $db;
	private $dbAlias		= 'addybase';

	private $tb_article	= 'article';
	private $tb_image 	= 'image';
	private $tb_slide		= 'slide';
	
	
	private $cols_article	= array('article_id', 'article_key', 'image_name', 'article', 'create_time', 'title', 'like_num', 'discuss_num', 'type', 'scan_num');
	private $cols_image	= array('id', 'image_id', 'image_path', 'image_name', 'create_time');
	private $cols_slide	= array('id', 'img_name', 'url', 'title', 'create_time');
	
	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}
	public function getArticles($params = array(), $orderby = 'create_time  desc', $limit = '') {
		$rs = $this->db->select($this->tb_article, $this->cols_article, $params, $orderby, $limit);
	 	return $rs;
	}
	public function countArticles($params = array()){
		$rs = $this->db->count($this->tb_article, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return false;
	}
	/**
	 * 增加slide图片
	 * @param unknown_type $params
	 */
	public function addSlide($params = array()){
		$rs = $this->db->insert($this->tb_slide, $params);
		return $rs;
	}
	/**
	 * 查询slide图片
	 * @param unknown_type $params
	 * @param unknown_type $orderby
	 * @param unknown_type $limit
	 */
	public function getSlides($params = array(), $orderby = 'create_time  desc', $limit = ''){
		$rs = $this->db->select($this->tb_slide, $this->cols_slide, $params, $orderby, $limit);
		return $rs;
	}
}