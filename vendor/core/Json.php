<?php
class View_Json extends View {
	public function renderer() {
		echo json_encode($this->variables);
	}
}