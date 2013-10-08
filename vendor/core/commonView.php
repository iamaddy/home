<?php
class CommonView extends View {
	public function renderer() {
		echo $this->variables;
	}
}