<?php
class IndexController extends AbstractController {
	private  $model;
	public function __construct() {
		$this->model = new IndexModel();
	}
	public function indexAction(){
		$data = array();
		$data['news'] = $this->model->getNews();
		return  new View(array('data' => $data));
	}
	public function chargeAction(){
		
	}
	public function addAction(){
		$this->hasPower();
	}
	public function detailsAction(){
		$data = array();
		$params['id'] = $this->get('id', 'int');
		$data['news'] = $this->model->getNews($params);
		return  new View(array('data' => $data));
	}
	public function saveAction(){
		$this->hasPower();
		$params['title'] = $this->post('title', 'str', ''); 
		$params['content'] = $this->post('content', 'str', ''); 
		if($params['title'] === '' || $params['content'] === ""){
			return $this->returnStatusView(false);
		}
		$params['create_date'] 		= date('Y-m-d H:i:s');
		$ret = $this->model->addNews($params);
		return $this->returnStatusView($ret);
	}
}