<?php

class page_masters_item extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add('CRUD',$acl->getPermissions());
		$crud->setModel('Item');

		if($crud->form){
			$crud->form->getElement('category_id')->setEmptyText('---')->setAttr('class','hindi');
		}

		if($crud->grid){
			$crud->grid->addFormatter('category', 'hindi');
			$crud->grid->addPaginator();
			$crud->grid->removeColumn('CurrentInwardStock');
			$crud->grid->removeColumn('TotalInwardStock');
			$crud->grid->removeColumn('TotalIssued');
			$crud->grid->removeColumn('current_Issued');
			$crud->grid->removeColumn('consume');
			$crud->grid->removeColumn('instock');
		}
	}
}