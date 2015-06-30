<?php

class Model_Students_Current extends Model_Student{
	function init(){
		parent::init();
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		 
	}

}