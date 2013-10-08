<?php
class JsonView extends View {
	public function renderer() {
		echo json_encode($this->variables);
	}
}