<?php
class page_student_marksheet extends Page{
	function page_index(){
		// parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add("Form",null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setEmptyText("----")->validateNotNull();
		$class_field->setAttr('class','hindi');
		$c=$this->add("Model_Class");

		$student_feild=$form->addField('dropdown','students')->setEmptyText("-----")->setAttr('class','hindi');

		$s=$this->add("Model_Students_Current");

		$form->addSubmit('Show');

		if($_GET['class_id']){
			$s->addCondition('class_id',$_GET['class_id']);
		}

		$s->_dsql()->order('roll_no');

		$class_field->setModel($c);
		$student_feild->setModel($s);

		$class_field->js('change',$form->js()->atk4_form('reloadField','students',array($this->api->url(),'class_id'=>$class_field->js()->val())));

		if($form->isSubmitted()){
			// $form->js()->univ()->successMessage('Hi')->execute();
			if($form->get('students'))
				$this->js()->univ()->newWindow($this->api->url("student_msview",array('class'=>$form->get('class'),'student'=>$form->get('students'))),null,'height=689,width=1246,scrollbar=1')->execute();
			else
				$this->js()->univ()->newWindow($this->api->url("student_mslist",array('class'=>$form->get('class'))),null,'height=689,width=1246,scrollbar=1')->execute();
		}
	}

}