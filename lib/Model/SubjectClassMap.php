<?php

class Model_SubjectClassMap extends Model_SubjectClassMapAll{
	function init(){
		parent::init();
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		
	}
}