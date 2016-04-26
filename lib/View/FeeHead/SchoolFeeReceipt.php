<?php
class View_FeeHead_SchoolFeeReceipt extends View{
	public $inward;
	public $outward;
	public $enquiry;

	function init(){
		parent::init();
		$feehead=$_GET['FeeHead'];
		$student=$_GET['student'];
		$class=$_GET['class'];
		$date=$_GET['date'];
		$amount=$_GET['amount'];
		
		$student_model=$this->add('Model_Student');
		$student_model->load($student);

		$class_model=$this->add('Model_Class');
		$class_name=$class_model->load($class)->get('class_name');

		$this->template->trySet('student_name',$student_model['fname']);
		$this->template->trySet('father_name',$student_model['father_name']);
		$this->template->trySetHTML('amount',$amount);
		$this->template->trySetHTML('date',date('d-M-Y',strtotime($date)));
		$this->template->trySetHTML('class',$class_name);
		$this->template->trySetHTML('fee_head',$feehead);
	}

	function defaultTemplate(){
		return array('view/feesreceipt/annual');
	}
}
