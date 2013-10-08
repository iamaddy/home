<?php
class Module extends AbstractModule {
	public function _autoloadConfig() {
		return include dirname(__FILE__).'/Config/config.php';
	}
}