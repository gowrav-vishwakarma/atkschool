<?php
class Model_Sessions_Current extends Model_Session {
	function init(){
		parent::init();
		$this->addCondition('iscurrent',true);
	

	}
}