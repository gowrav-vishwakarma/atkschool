<?php

class page_masters_welcome extends Page{
	function init(){
		parent::init();
		// $acl=$this->add('xavoc_acl/Acl');
		$image=$this->add('Model_MS_WelcomeImage');
		// $image->addCondition('is_active',true);
		$crud=$this->add('CRUD');//,$acl->getPermissions());
		$crud->setModel($image);
		

		
		
	}
}