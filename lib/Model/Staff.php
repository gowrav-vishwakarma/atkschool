<?php

class Model_Staff extends Model_Staff_All {
	function init(){
		parent::init();

		$this->addCondition('is_active',true);
	}
}