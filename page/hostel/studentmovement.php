<?php
class page_hostel_studentmovement extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setAttr('class','hindi')->setEmptyText('-----');
		$student_field=$form->addField('dropdown','student')->setAttr('class','hindi')->setEmptyText('-----');
		$form->addSubmit('Get List');

		$v=$this->add('View_StudentMovement');

		$c=$this->add('Model_Class');
		$s=$this->add('Model_Hosteler');
		$s->add('Controller_CurrentSession');
		

		if($_GET['class_id']){
			$s->addCondition('class_id',$_GET['class_id']);
			$s->_dsql()->del('order')->order('fname','asc');
		}	

		$s->_dsql()->order('fname','asc');
		$class_field->setModel($c);
		$student_field->setModel($s);
		// $s->debug();
		$class_field->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$class_field->js()->val())));

		$hm=$this->add('Model_Hosteler');
// 
		// $hm['session_id']=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
		if($_GET['hostler_id']){
			$hm->addCondition('id',$_GET['hostler_id']);
		}else{
			$hm->addCondition('id',0);
		}
		
		$v->setModel($hm);

		if($form->isSubmitted()){
			// throw $this->exception($form->get('student'));
			$v->js()->reload(array('hostler_id'=>$form->get('student')))->execute();
		}

	}
	
}