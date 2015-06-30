<?php

class Model_ExamClassMapAll extends Model_Table {
	var $table= "exam_map";
	function init(){
		parent::init();
		$this->hasOne('Exam','exam_id')->display('hindi');
		$this->hasOne('Class','class_id');
		$this->hasOne('Session','session_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('exam_id')->fieldQuery('name');
		})->display('hindi');

		$this->hasMany('ExamClassSubjectMap','exammap_id');

	}

	function promote($from_session, $to_session){

		$old_mapping=$this->add('Model_ExamClassMapAll');
		$old_mapping->addCondition('session_id',$from_session);

		foreach ($old_mapping as $old) {

			$new=$this->add('Model_ExamClassMapAll');
			$new['exam_id']=$old['exam_id'];
			$new['class_id']=$old['class_id'];
			$new['session_id'] = $to_session;
			$new->save();
			$new->destroy();
		}
	}


	function createNew($exam,$class,$session){
		$this['class_id']=$class->id;
		$this['exam_id']=$exam->id;
		$this['session_id']=$session->id;
		$this->save();
		return $this;
	}
}