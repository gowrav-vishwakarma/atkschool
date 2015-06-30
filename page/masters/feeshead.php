<?php

class page_masters_feeshead extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add('CRUD',$acl->getPermissions());
		$crud->setModel('FeesHead');
	}
}