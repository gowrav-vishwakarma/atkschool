<?php
class MyMenu extends Menu{
	function init(){
		parent::init();

		$this->addMenuItem('h','Home');
	}
}