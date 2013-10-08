<?php
class CommentWidget extends AbstractWidget {
	public function excute($params = array()) {
		$data = array();
		$data['comment'] = isset($params['comment']) ? $params['comment']: array();
		$data['comment_count'] = isset($params['comment_count']) ? $params['comment_count']: 0;
		return new view($data);
	}
}