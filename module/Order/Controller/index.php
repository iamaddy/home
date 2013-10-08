<?php

class IndexController extends AbstractController {
	private  $model;
	public function __construct(){
		$this->model = new IndexModel();
	}
	public function IndexAction() {
		session_start();
		$data = array();
		return new View(array('data' => $data));
	}
	public function AdminAction(){
		$this->hasPower();
		$data = array();
		$params = array();
		$phone = $this->get('phone', 'str', '');
		$date = $this->get('date', 'str', '');
		if($phone !== ""){
			$params['phone_number LIKE'] = '%'.$phone.'%';
		}
		if($date !== ""){
			$params['create_date LIKE'] = '%'.$date.'%';
		}
		$data['orders'] = $this->model->getOrders($params, 'create_date desc', $this->getLimit(20));
		$data['num'] = $this->model->countOrders($params);
		return new View(array('data' => $data));
	}
	public function listAction(){
		session_start();
		$data = array();
		$params = array();
		$params['phone_number'] = $_SESSION['phone_num'];
		$data['orders'] = $this->model->getOrders($params, 'create_date desc', $this->getLimit(20));
		$data['num'] = $this->model->countOrders($params);
		return new View(array('data' => $data));
	}
	public function detailsAction(){
		$data = array();
		$params['id'] = $this->get('id', 'int');
		$data['order'] = $this->model->getOrders($params);
		if(count($data['order']) > 0){
			$data['objects'] = $this->model->getObjects(array('order_id' =>$params['id']));
		} else {
			$data['objects'] = array();
		}
		return  new View(array('data' => $data));
	}

	public function SaveAction(){
		$params = $this->parseParam();
		$bobject = $this->post('bobject', 'arr'); 
		$sobject = $this->post('sobject', 'arr'); 
		$ret = $this->model->addOrder($params);
		foreach ($bobject as $key => $value){
			$bobject[$key]['type'] = 1;
			$bobject[$key]['order_id'] = $ret;
			$bobject[$key]['create_date'] 		= date('Y-m-d H:i:s');
		}
		foreach ($sobject as $key => $value){
			$sobject[$key]['type'] = 0;
			$sobject[$key]['order_id'] = $ret;
			$sobject[$key]['create_date'] 		= date('Y-m-d H:i:s');
		}
		$object = array_merge($bobject, $sobject);
		$rets = $this->model->addObjects($object);
		session_start();
		$_SESSION['phone_num'] = $params['phone_number'];
		return new JsonView(array('status' => $ret));
	}
	public function parseParam(){
		$params['phone_number'] 	= $this->post('phone', 'str', '');
		$params['name'] 					= $this->post('pname', 'str', '');
		$params['person'] 				= $this->post('nperson', 'str', '');
		$params['from_address'] 	= $this->post('oaddress', 'str', '');
		$params['from_left'] 			= $this->post('oleft', 'int', 1);
		$params['from_floor'] 			= $this->post('ofloor', 'int', 1);
		$params['from_fee'] 			= $this->post('ofee', 'float', 0);
		$params['to_address'] 		= $this->post('naddress', 'str', '');
		$params['to_floor'] 				= $this->post('nfloor', 'int', 1);
		$params['to_fee'] 				= $this->post('nfee', 'float', 0);
		$params['to_left'] 				= $this->post('nleft', 'int', 1);
		$params['time'] 					= $this->post('ntime', 'float', 0);
		$params['distance'] 				= $this->post('ndis', 'float', 0);
		$params['box'] 					= $this->post('box', 'int', 0);
		$params['is_precious'] 		= $this->post('is_precious', 'int', 0);
		$params['is_pack'] 				= $this->post('is_pack', 'int', 1);
		$params['is_dismount'] 		= $this->post('is_dismount', 'int', 1);
		$params['precious_name'] 	= $this->post('precious_name', 'str', '');
		$params['create_date'] 		= date('Y-m-d H:i:s');
		return $params;
	}
}
