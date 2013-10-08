<?php
class PagerWidget extends AbstractWidget {
	public function excute($params = array()) {
		$data = array();
		//总数量
		$data['num'] = isset($params['num']) && is_numeric($params['num']) && $params['num'] > 0? $data['num'] = $params['num']: $data['num'] = 0;
		if(!$data['num']) return new View($data);

		$data['page'] = isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0? $data['page'] = $params['page']: $data['page'] = 1;

		//每页显示条数
		$data['size'] = isset($params['size']) && is_numeric($params['size']) && $params['size'] > 0? $data['size'] = $params['size']: $data['size'] = 30;

		//页数
		$data['pagenum'] = ceil($data['num']/$data['size']);

		return new view($data);
	}
}