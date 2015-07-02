<?php

class View_Scholar_Details extends CompleteLister{

	var $rows=1;
	function init(){
		parent::init();


	}

	function setModel($model){
		$model->addExpression('class_name')->set(function ($m,$q){
			return $m->refSQL('class_id')->fieldQuery('class_name');
        });

		// throw new Exception($model->get('class_name'), 1);
		
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

		if(!in_array($this->model->get('class_name'), array('10','12','8'))){
			$this->current_row['class_admission_date'] = " ";
		}else
			$this->current_row['class_admission_date'] = date('d-M-Y',strtotime($this->model->ref('session_id')->get('start_date')));
		
		if(in_array($this->model->get('class_name'), array('10','12','8'))){
			$this->current_row['class_end_date'] = " ";
			$this->current_row['total_meetings'] = " ";
			$this->current_row['all_attendance'] = " ";
			
		}else
			$this->current_row['class_end_date'] = date('d-M-Y',strtotime($this->model->ref('session_id')->get('end_date')));
		// $this->current_row['total_meetings'] = $this->model->ref('Student')->ref('Attendance')->get('end_date');

	}

	function defaultTemplate(){
		return array('view/tcdetails');
	}
}