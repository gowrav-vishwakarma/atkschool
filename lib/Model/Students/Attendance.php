<?php
class Model_Students_Attendance extends Model_Table{
 	var $table="student_attendance";
	function init(){
		parent::init();

		$this->hasOne('Class','class_id');
		$this->hasOne('Student','student_id');
		$this->hasOne('Session','session_id');

		$this->addField('month')->setValueList(array('1'=>'Jan',
            							'2'=>'Feb',
            							'3'=>'March',
            							'4'=>'April',
            							'5'=>'May',
            							'6'=>'Jun',
            							'7'=>'July',
            							'8'=>'Augest',
            							'9'=>'Sep',
            							'10'=>'Oct',
            							'11'=>'Nov',
            							'12'=>'Dec'
            							));
		$this->addField('total_attendance');
		$this->addField('present');

		$this->addExpression("roll_no")->set(function($m,$q){
			return $m->refSQL('student_id')->fieldQuery('roll_no');
		});

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if($this['present'] > $this['total_attendance'])
			$this->owner->js()->univ()->errorMessage("Present can not be greater then Total Attendance")->execute();
	}
}