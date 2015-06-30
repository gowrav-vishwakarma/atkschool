<?php
class Model_Students_Marks extends Model_Table{
	var $table="student_marks";
	function init(){
		parent::init();

		$this->hasOne('Student','student_id');
		$this->hasOne('ExamClassSubjectMapAll','examsub_map_id');
		$this->addField('marks');

		// $this->addExpression("roll_no")->set(function($m,$q){
		// 	return $m->refSQL('student_id')->fieldQuery('roll_no');
		// });
		$this->addExpression('max_marks')->set(function($m,$q){
			return $m->refSQL('examsub_map_id')->fieldQuery('max_marks');
		});

		$this->addExpression('class')->set(function($m,$q){
			return $m->refSQL('student_id')->fieldQuery('class');
		});
		$this->addExpression('subject')->set(function($m,$q){
			return $m->refSQL('examsub_map_id')->fieldQuery('subject');
		});


		$this->addHook('beforeSave',$this);
	}
		function beforeSave(){
			
			if($this['marks'] > $this->ref('examsub_map_id')->get('max_marks'))		
				 $this->owner->js()->univ()->errorMessage('Marks can not be greater then Max Marks')->execute();
		}
}