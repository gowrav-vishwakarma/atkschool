<?php
class Model_ExamClassSubjectMap extends Model_ExamClassSubjectMapAll {
	function init(){
		parent::init();
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));		
	}
}