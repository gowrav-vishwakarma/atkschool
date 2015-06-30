<?php
class Model_Scholars_Unalloted extends Model_Scholar{
	function init(){
		parent::init();
		$this->addCondition('active_in_session',false);
	}
}