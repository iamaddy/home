<?php
class IndexController extends AbstractController {
	public function __construct() {
		
	}
	public function indexAction(){
		
	}
	public function getLinksAction(){
		$config = $this->getModule()->getConfig()->get('links');
		return new JsonView(array('links' => $config));
	}
}