<?php
class page_resultonline extends Page {
	function init(){
		parent::init();


		$form=$this->add('Form');
		$class_field=$form->addField('dropdown','class')->setAttr('class','hindi');
		$class_field->setModel('Class');
		$form->addField('line','roll_no');
		$form->addField('DatePicker','date_of_birth');

		$form->addSubmit('Get Result');


		
		if($form->isSubmitted()){
			$student=$this->add('Model_Students_Current');
			$student->addCondition('roll_no',$form->get('roll_no'));
			$student->tryLoadAny();
			if(strtotime($student->ref('scholar_id')->get('dob'))!=strtotime($form->get('date_of_birth')))
						$form->displayError('date_of_birth','Not Valid');
			if(!$student->checkRollNo($form->get('roll_no'),$form->get('class')))
						$form->displayError('roll_no','Not Valid');
			if($student['is_result_stop'])
						$form->displayError('roll_no','Result Stopped');

				$this->js()->univ()->newWindow($this->api->url("student_msview",array('class'=>$form->get('class'),'student'=>$student->id,'delete_front'=>true,'stop_result'=>$student['is_result_stop'])),null,'height=689,width=1246,scrollbar=1')->execute();
			
		}

	}

	function render(){
		// $this->api->template->del('header');
		// $this->api->template->del('logo');
		$this->api->template->del('Menu');
		// $this->api->template->del('date');
		$this->api->template->del('welcome');
		// $this->api->template->del('footer_text');

		parent::render();
	}
}