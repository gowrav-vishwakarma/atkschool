<?php
class page_student_feereciept extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		
		$form=$this->add('Form',null,null,array('form_horizontal'));

		$form->addField('dropdown','class');
	}

}