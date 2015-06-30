<?php

class page_masters_party extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add('CRUD',$acl->getPermissions());

		if($crud->grid){
			$crud->grid->addColumn('sno','sno');
		}
		$crud->setModel('Party',array('sno','ename','name','contact','address','is_mesh_supplier'),array('sno','ename','name','contact','address','is_mesh_supplier'));
	
	}
}