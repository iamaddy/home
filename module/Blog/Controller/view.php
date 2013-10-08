<?php
class ViewController extends AbstractController {
	private  $model;
	public function __construct(){
		$this->model = new IndexModel();
	}
	// 获取博客
	public function IndexAction() {
		$article_key = $this->get('p', 'str');
		$data = array();
		$rs = $this->model->getBlog(array('article_key' => $article_key, 'isdel' => 0));
		$rs_comment = $this->model->getComments(array('article_key' => $article_key, 'isdel' => 0), 'create_time desc');
		$ids = explode(',', $rs['type']);
		$tags = array();
		$category = $this->model->getCategories(array('category_id IN' => $ids));
		$data['tags'] = $category;
		$data['blog'] = $rs;
		$data['comment'] = $rs_comment;
		$data['comment_count'] = $this->model->countComment(array('article_key' => $article_key, 'isdel' => 0));
		return new View(array('data' => $data));
	}
	// 编辑博客
	public function editAction() {
		$article_key = $this->get('p', 'str');
		$data = array();
		$rs = $this->model->getBlog(array('article_key' => $article_key));
		$data['blog'] = $rs;
		$tags = array();
		$ids = explode(',', $rs['type']);
		$category = $this->model->getCategories();
		$data['tags'] = $category;
		return new View(array('data' => $data));
	}
	/**
	 * delete blog
	 */
	public function delAction(){
		$params['article_key'] = $this->get('p', 'str', '');
		if($params['article_key'] === '') return $this->returnStatusView(false);
		$ret = $this->model->delBlog($params);
		return $this->returnStatusView($ret);
	}
	/**
	 * 赞
	 */
	public function likeAction(){
		$columns['article_key']  	= $this->get('id', 'str', '');
		if($columns['article_key'] === '') $this->returnStatusView(false);
		$columns['like_num'] 		= $this->get('num', 'int', 0);
		if($columns['article_key']){
			$params['article_key'] = $columns['article_key'];
			unset ($columns['article_key']);
		}
		$rs = $this->model->updateBlog($params, $columns);
		return $this->returnStatusView($rs);
	}
	/**
	 * delete comment
	 */
	public function delCommentAction(){
		$params['id'] = $this->get('id', 'int', '');
		if($params['id'] === '') return $this->returnStatusView(false);
		$ret = $this->model->delComment($params);
		return $this->returnStatusView($ret);
	}
	
	public function getCategoryAction(){
		$category = $this->model->getCategories();
		return new JsonView(array('category' => $category));
	}
	public function addAction() {
		$rs = $this->model->getCategories();
		$data['tags'] = $rs;
		return new View(array('data' => $data));
	}
	// 添加留言
	public function sayAction(){
		$article_id = $this->post('p', 'str');
		$text 		= $this->post('comment', 'str', '');
		$author 	= $this->post('author', 'str', '');
		$mail		= $this->post('mail', 'str', '');
		if($article_id === '' || $text == '' || $author == '' || $mail == ''){
			return $this->returnStatusView(false);
		}
		$data = array();
		$data['article_key'] = $article_id;
		$data['content']	= $text;
		$data['create_time']= date('Y-m-d H:i:s', time());
		$data['mail']		= $mail;
		$data['author']		= $author;
		$data['ip']			= $_SERVER['REMOTE_ADDR'];
		$rs = $this->model->addComment($data);
		if($rs) {
			$ret = $this->model->getBlog(array('article_key' => $data['article_key']));
			$ret = $this->model->updateBlog(array('article_key' => $data['article_key']), array('discuss_num' => $ret['discuss_num']+ 1));
		}
		return new JsonView(array('id' => $rs));
	}
	/**
	 * 添加留言
	 */
	public function saveAction() {
		$params = $this->getParams();
		if(!$params['article_key']){
			$params['article_key'] = md5($params['create_time'] . rand(0, 15000));
		}
		$rs = $this->model->addBlog($params);
		return $this->returnStatusView($rs);
	}
	
	public function updateAction() {
		$columns = $this->getParams();
		if($columns['article_key']){
			$params['article_key'] = $columns['article_key'];
			unset ($columns['article_key']);
		}
		$rs = $this->model->updateBlog($params, $columns);
		return $this->returnStatusView($rs);
	}
	private function getParams(){
		$title = $this->post('title', 'str');
		$text = $this->post('text', 'str');
		$tags = $this->post('tags', 'str');
		$image_path = $this->post('bill', 'str', '');
		if($tags){
			$tags = $this->addCategories($tags);
		}
		$params = array();
		$params['title'] = $title;
		$params['article'] = $text;
		preg_match('/src="(.*?)"/', $image_path, $match);
		$params['image_name']	= $match[1];
		$params['type'] = $tags;
		$params['create_time'] = date('Y-m-d H:i:s', time());
		$params['change_time'] = date('Y-m-d H:i:s', time());
		if($this->post('article_key', 'str', '')){
			$params['article_key'] = $this->post('article_key');
		}
		return $params;
	}
	/**
	 * 添加tag
	 * 过滤数据库中已经存在的tag
	 * 返回当前blog对于的tag 的ids
	 * @param unknown_type $tags
	 */
	private function addCategories($tags){
		$ids = array();
		$tag = preg_split('/\s+/', $tags);
		$category = $this->model->getCategories();
		$name = array();
		foreach ($category as $key => $value){
			$name[] = $value['category_name'];
			if(in_array($value['category_name'], $tag)){
				$ids[] = $value['category_id'];
			}
		}
		$new_tag = array_diff($tag, $name);
		foreach ($new_tag as $value){
			$rs = $this->model->addCategory(array('category_name' => $value, 'create_time' => date('Y-m-d H:i:s', time())));
			if($rs) {
				$ids[] = $rs;
			}
		}
		return implode(',', $ids);
	}
	
}