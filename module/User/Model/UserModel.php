<?php

class UserModel extends AbstractModel {
	private $dbAlias = 'lodatabase';
	private $table   = 'user';
	private $db;
	private $cols_user = array('id', 'name', 'password', 'create_time');

	// 构造器
	public function __construct() {
		$this->db = Db::instance($this->dbAlias);
	}

	// 登陆操作
	public function login($username, $password) {
		$rs = $this->db->one($this->table, $this->cols_user, array('name' => $username));
		if($rs['password'] === md5($password)){
			return true;
		} else {
			return false;
		}
	}

	// 退出登陆操作
	public function logout() {
		return false;
	}

	/**
	 * 根据UID获取用户信息
	 * @param  int  $uid
	 * @return array
	 */
	public function userInfo($uid) {
		$params  = array('uid' => $uid);
		$columns = '*';

		return $this->db->one($this->table, $columns, $params);
	}

	/**
	 * 搜索用户
	 * @param string $username
	 */
	public function search($username) {
		$params  = array('name like' => '%'.$username.'%');
		$columns = '*';

		return $this->db->select($this->table, $columns, $params);
	}

//*****************私有操作***************************************************************************//
	// 设置在线
	private function setOnline($uid) {

	}
	// 设置离线
	private function setOffline($uid) {

	}
}