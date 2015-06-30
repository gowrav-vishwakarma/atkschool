<?php

class Model_ExamClassMap extends Model_ExamClassMapAll {
	function init(){
		parent::init();
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$this->hasMany("ExamClassSubjectMap","exammap_id");

		
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if(!$this->recall('keep_added',false)){
			$ecsm=$this->ref('ExamClassSubjectMap');
			$ecsm->addCondition('subject_id','in',$this->ref('class_id')->ref('SubjectClassMap')->dsql()->del('field')->field('subject_id'));
			// $ecsm->debug();
			if($ecsm->count()->getOne())
				throw $this->exception('You cannot remove class '. $this->ref('class_id')->get('name') .' From This Exam as the subjects of this class are in use of this exam');
		}
	}
}