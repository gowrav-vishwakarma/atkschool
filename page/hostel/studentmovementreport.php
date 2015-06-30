<?php

class page_hostel_studentmovementreport extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setEmptyText('---')->setAttr('class','hindi');
		$class_field->setModel('Class');
		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date','Before Date');
		$student_field=$form->addField('dropdown','student')->setEmptyText('---')->setAttr('class','hindi');
		$form->addField('dropdown','status')->setValueList(array('inward'=>'inward','outward'=>'outward'))->setEmptyText('---');
		$form->addSubmit('Filter');

		$students_m=$s=$this->add('Model_Hosteler');
		$students_m->add('Controller_CurrentSession');
		if($_GET['filter_class'])
			$students_m->addCondition('class_id',$_GET['filter_class']);
		// else
			// if(!$_GET['filter']) $students_m->addCondition('class_id',-1);

		$student_field->setModel($students_m);

		$class_field->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'filter_class'=>$class_field->js()->val())));

		$crud=$this->add('CRUD',array('allow_add'=>false));
		$m=$this->add('Model_Students_Movement');

		$m->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$m['session_id']=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');

		$this->api->stickyGET('filter');
		$this->api->stickyGET('class_id');
		$this->api->stickyGET('from_date');
		$this->api->stickyGET('to_date');
		$this->api->stickyGET('student');
		$this->api->stickyGET('status');

		if($_GET['filter']){
			if($_GET['class_id']) $m->addCondition('class_id',$_GET['class_id']);
			if($_GET['from_date']) $m->addCondition('date','>=', $_GET['from_date']);
			if($_GET['to_date']) $m->addCondition('date','<=', $_GET['to_date']);
			if($_GET['student']) $m->addCondition('student_id',$_GET['student']);
			if($_GET['status']) $m->addCondition('purpose',$_GET['status']);
		}
		// else{
		// 	$m->addCondition('class_id',-1);
		// }

		$m->addExpression('guardian_image')->set(function($m,$q){
			return $m->refSQL('gaurdian_id')->fieldQuery('image_url');
		});


		$m->addExpression('father_name')->set(function ($m,$q){
			return $m->refSQL('student_id')->fieldQuery('father_name');

		})->display('hindi');
	

		$crud->setModel($m,array('student',"father_name",'gaurdian','date','purpose','remark','class','guardian_image'));
		if($crud->grid){
			$crud->grid->setFormatter('student','hindi');
			$crud->grid->setFormatter('purpose','attendance');
			$crud->grid->setFormatter('gaurdian','hindi');
			$crud->grid->setFormatter('class','hindi');
			$crud->grid->setFormatter('guardian_image','picture');
			$crud->grid->addPaginator(15);
		}
		if($crud->form){
			// $crud->form->getElement('student_id')->destroy();
			// $crud->form->getElement('gaurdian_id')->destroy();
		}

		if($form->isSubmitted()){
			// throw $this->exception($form->get('student'));
			$crud->grid->js()->reload(array(
				'class_id'=>$form->get('class'),
				'from_date'=>$form->get('from_date'),
				'to_date'=>$form->get('to_date'),
				'student'=>$form->get('student'),
				'status'=>$form->get('status'),
				'filter'=>1

				))->execute();
		}

	}
}