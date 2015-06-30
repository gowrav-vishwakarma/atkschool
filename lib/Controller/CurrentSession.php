<?php

class Controller_CurrentSession extends AbstractController{
	function init(){
		parent::init();
		if(!($this->owner instanceof Model_Table)) throw $this->exception('Controller applied on non Model_table Object');

		$this->owner->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));

	}
}