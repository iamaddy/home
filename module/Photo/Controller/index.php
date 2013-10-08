<?php
class IndexController extends AbstractController {
	private  $model;
	public function __construct() {
		$this->model = new IndexModel();
	}
	public function indexAction(){
		$data = array();
		return new View(array('data' => $data));
	}
	public function addPhotoAction(){
		$des = $this->post('des', 'str', '');
		$title = $this->post('title', 'str', '');
		$img_path = $this->post('path', 'str', '');
		
		if(!$des || !$title || !$img_path){
			return $this->returnStatusView(false);
		}
		$params['describe'] 	= $des;
		$params['title']		= $title;
		$params['image_name'] = $title;
		$params['image_path'] = $img_path;
		$params['create_time'] = date('Y-m-d H:i:s', time());
		$params['shoot_time'] = date('Y-m-d H:i:s', time());
		$params['image_id'] = md5($img_path . rand(0, 10000));
		
		$rs = $this->model->addPhoto($params);
		return $this->returnStatusView($rs);
	}
	public function addAction(){
		return new View();
	}
	public function getPhotosAction(){
		$page = $this->get('page', 'int', 1);
		$size = $this->get('size', 'int', 20);
		$limit = ($page - 1) * $size . ',' .  $size;
		$rs = $this->model->getPhotos( array(), 'create_time desc', $limit);
		return new JsonView($rs);
	}
	public function viewAction(){
		$data = array();
		$params = array();
		$params['image_id'] = $this->get('key', 'str', '');
		$data['img'] = $this->model->getOnePhoto($params);
		$data['comment_count'] = $this->model->countImgComment($params);
		$data['comment'] = $this->model->getImgComments($params);
		/* var_dump($data);exit(); */
		return new View(array('data' => $data));
	}
	// 添加留言
	public function sayAction(){
		$image_id = $this->post('key', 'str');
		$text 		= $this->post('comment', 'str', '');
		$author 	= $this->post('author', 'str', '');
		$mail		= $this->post('mail', 'str', '');
		if($image_id === '' || $text == '' || $author == '' || $mail == ''){
			return $this->returnStatusView(false);
		}
		$data = array();
		$data['image_id'] = $image_id;
		$data['comment']	= $text;
		$data['create_time']= date('Y-m-d H:i:s', time());
		$data['mail']		= $mail;
		$data['author']		= $author;
		$data['ip']			= $_SERVER['REMOTE_ADDR'];
		$rs = $this->model->addImgComment($data);
		return $this->returnStatusView($rs);
	}
}