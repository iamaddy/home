<?php
class IndexModel extends AbstractModel {
	private $db;
	private $dbAlias		= 'addybase';

	private $tb_article		= 'article';
	private $tb_image 		= 'image';
	private $tb_comment 	= 'comment';
	private $tb_category 	= 'category';

	private $cols_article			= array('article_id', 'article_key', 'image_name', 'article', 'create_time',
			 										'title', 'like_num', 'discuss_num', 'type', 'scan_num', 'isdel');
	private $cols_image			= array('id', 'image_id', 'image_path', 'image_name', 'create_time');
	private $cols_comment 	= array('id', 'article_key', 'content', 'ip', 'create_time', 'mail', 'author', 'isdel');
	private $cols_category  	= array('category_id', 'category_name', 'create_time');
	
	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}
	public function getArticles($params = array(), $orderby = 'create_time  desc', $limit = ''){
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
	public function getBlog($params = array(), $orderby = '', $limit = ''){
		$rs = $this->db->one($this->tb_article, $this->cols_article, $params, $orderby, $limit);
		return $rs;
	}
	public function addBlog($params = array()) {
		$rs = $this->db->insert($this->tb_article, $params);
		return $rs;
	}
	public function delBlog($params = array()){
		$rs = $this->db->update($this->tb_article, array('isdel' => 1) , $params);
		return $rs;
	}
	public function updateBlog($params = array(), $columns){
		$rs = $this->db->update($this->tb_article, $columns, $params);
		return $rs;
	}
	/**
	 * add some comment about the blog
	 * @param unknown $params
	 * @return Ambigous <boolean, unknown>
	 */
	public function addComment($params = array()){
		$rs = $this->db->insert($this->tb_comment, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
	/**
	 * 获取评论
	 * @param unknown $params
	 * @param string $orderby
	 * @param string $limit
	 * @return Ambigous <boolean, unknown>
	 */
	public function getComments($params = array(), $orderby = '', $limit = ''){
		$rs = $this->db->select($this->tb_comment, $this->cols_comment, $params, $orderby, $limit);
		return $rs;
	}
	/**
	 * count the comment
	 * @param unknown $params
	 * @return Ambigous <boolean, number, string, multitype:>|boolean
	 */
	public function countComment($params = array()){
		$rs = $this->db->count($this->tb_comment, $params);
		if (is_int($rs) && $rs !== 0) {
			return $rs;
		}
		return 0;
	}
	/**
	 * delete comment
	 * @param unknown_type $params
	 */
	public function delComment($params = array()){
		$rs = $this->db->update($this->tb_comment, array('isdel' => 1) , $params);
		return $rs;
	}
	
	public function getCategories($params = array(), $orderby = 'create_time  desc', $limit = ''){
		$rs = $this->db->select($this->tb_category, $this->cols_category, $params, $orderby, $limit);
		return $rs;
	}
	public function addCategory($params = array()){
		$rs = $this->db->insert($this->tb_category, $params);
		if ($rs === true) {
			return $this->db->lastInsertId();
		}
		return false;
	}
}