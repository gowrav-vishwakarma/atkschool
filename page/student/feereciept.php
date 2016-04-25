<?php
class page_student_feereciept extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class=$this->add('Model_Class');
		$class_field=$form->addField('dropdown','class');
		$class_field->setEmptyText('Please Select Class');
		$class_field->setModel($class);
	}

}