<?php

class View_Scholar extends View{
	function init(){
		parent::init();

	}

	function setModel($scholar){
		$student=$scholar->ref('Student');

		$student->addExpression('class_section')->set(function ($m,$q){
			return $m->refSQL('class_id')->fieldQuery('section');
                })->display('hindi');

		$student->addExpression('className')->set(function ($m,$q){
			return $m->refSQL('class_id')->fieldQuery('class_name');
                })->display('hindi');

		$this->add('View_Scholar_Details',null,'details_location')->setModel($student);

		$this->template->tryset('date_of_birth',date('d-m-Y',strtotime($scholar['dob'])));
		$this->template->tryset('admissiondate',date('d-m-Y',strtotime($scholar['admission_date'])));


		parent::setModel($scholar);
	}

	function defaultTemplate(){
		return array('view/tc');
	}
}