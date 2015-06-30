<?php

class page_staff_add extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud = $this->add('CRUD',$acl->getPermissions());
		if($crud->form)
			$crud->form->add('Model_Staff_All')->getElement('sex')->display('radio')->enum(array('M','F'));
		if($crud->grid)
			$crud->grid->addColumn('sno','sno');
		$crud->setModel('Staff_All',array('doj','ename','hname','designation','father_name','mother_name','guardian_name','is_active','dob','contact','pan_no','image','address','sex','accno','insurance_no','ofhostel','remarks',''),array('hname','designation','contact','is_active','image_url'));
	
	}
}