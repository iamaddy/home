<?php

class IndexController extends AbstractController {
	private  $model;
	public function __construct(){
		$this->model = new IndexModel();
	}
	public function IndexAction() {
		$data = array();
		$rs = $this->model->getArticles(array('isdel' => '0'), 'create_time desc', $this->getLimit(12));
		foreach ($rs as $key => $value){
			$rs[$key]['summary'] = $this->parseSummary($value['article']);
		}
		$data['article'] = $rs;
		$data['num'] = $this->model->countArticles(array('isdel' => '0'));
		return new View(array('data' => $data));
	}
	// 提取摘要 过滤相关的html标签
	private function parseSummary($content) {
		// $content = htmlspecialchars($content);
		$exp = '/<(.*?)>|&nbsp;/i';
		$result = preg_replace($exp, '', $content);
		return Util::utf_substr($result, 120);
	}
	public function AddAction(){
		return new View();
	}
	/**
	 * 保存slide图片
	 */
	public function SaveAction(){
		$title 	= $this->post('title', 'str', '');
		$url	= $this->post('url', 'str', '');
		$img	= $this->post('img', 'str', '');
		
		$params = array();
		$params['title'] = $title;
		$params['url']	= $url;
		preg_match('/src="(.*?)"/', $img, $match);
		$params['img_name']	= $match[1];
		$params['create_time']	= date('Y-m-d H:i:m');
		
		$ret = $this->model->addSlide($params);
		return $this->returnStatusView($ret);
	}
	public function getSlideAction($rs){
		$rs = $this->model->getSlides(array(), 'create_time desc', $this->getLimit(5));
		return new JsonView($rs);
	}
}
