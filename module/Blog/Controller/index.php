<?php
class IndexController extends AbstractController {
	private  $model;
	public function __construct(){
		$this->model = new IndexModel();
	}
	public function IndexAction() {
		$type = $this->get('t', 'str');
		if(!empty($type)) $params['type'] = $type;
		$data = array();
		$params['isdel'] = 0;
		$rs = $this->model->getArticles($params, 'create_time desc', $this->getLimit(12));
		foreach ($rs as $key => $value){
			$rs[$key]['summary'] = $this->parseSummary($value['article']);
		}
		$data['article'] = $rs;
		$data['num'] = $this->model->countArticles(array('isdel' => 0));
		$data['article'] = $rs;
		return new View(array('data' => $data));
	}
	public function parseSummary($value){
		// $value = htmlspecialchars($value);
		$Reg = '/<(.*?)>|&nbsp;/i';
		$ret = preg_replace($Reg, "", $value);
		return Util::utf_substr($ret, 120);
	}
}