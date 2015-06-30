<?php

class View_Scholar_Details extends CompleteLister{

	var $rows=1;
	function init(){
		parent::init();


	}

	function setModel($model){
		$model->addExpression('total_meetings')->set(function($m,$q){
			return $m->refSQL('Students_Attendance')->sum('total_attendance');
		});

		$model->addExpression('students_in_class')->set(function($m,$q){

			return "((select count(*) from `student` st2 where `st2`.`session_id` = `student`.`session_id` and `st2`.`class_id` = `student`.`class_id` ))";
		});	
		
		$model->addExpression('all_attendance')->set(function($m,$q){
            return $m->refSQL('Students_Attendance')->sum('present');
        });	


		parent::setModel($model);

		$extrarows=10 - $model->count()->getOne();
		for ($i=1; $i<=$extrarows; $i++) $this->add('View',null,'ExtraRows',array('view/extrarows'));
	}

	function formatRow(){
		// if($this->rows==1){
		// 	$this->current_row['no_of_students_rows'] = $this->model->count()->getOne();
		// }else{
		// 	$this->column['last_column']->destroy();
		// }
		$this->current_row['class_admission_date'] = $this->model->ref('session_id')->get('start_date');
		$this->current_row['class_end_date'] = $this->model->ref('session_id')->get('end_date');
		// $this->current_row['total_meetings'] = $this->model->ref('Student')->ref('Attendance')->get('end_date');

	}

	function defaultTemplate(){
		return array('view/tcdetails');
	}
}