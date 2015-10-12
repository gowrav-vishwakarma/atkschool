<?php
class page_store_iteminward extends Page {

	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');

		$pm=$this->add('Model_Party');
		// $pm->_dsql()->del('order')->order('ename','asc');
		$grid->setModel($pm);

		$grid->addColumn('Button','add_inward');
		if($_GET['add_inward']){
			$grid->js()->univ()->frameURL("Manage Bills",$this->api->url('./partybills',array('party_id'=>$_GET['add_inward'])))->execute();
		}
	}

	function page_partybills(){
		$this->api->stickyGET('party_id');
		$pm=$this->add('Model_Party');
		$pm->load($_GET['party_id']);

		$crud=$this->add('CRUD');
		$crud->setModel($pm->ref('Bill'),array('name','bill_date','inward_date','paid','cheque_date','cheque_number','no_of_items','bill_amount'));
		if($crud->grid){
			$crud->grid->addColumn('Expander','details');
		}
	}


	function page_partybills_details(){
		$v=$this->add('View')->setClass('atk-box ui-widget-content');
		$this->api->stickyGET('bill_master_id');
		$bill=$this->add('Model_Bill');
		$current_sess=$this->add('Model_Sessions_Current')->tryLoadAny();
		$bill->load($_GET['bill_master_id']);

		$crud=$v->add('CRUD');
		if($crud->form){
			$category_field=$crud->form->addField('dropdown','category')->addClass('hindi')->setEmptyText('Please Select');
			$category_field->setModel('Model_Item_Category');
		}
		$item_inward=$bill->ref('Item_Inward');
		$item_inward->addCondition('session_id',$current_sess->id);
		$crud->setModel($item_inward);
		if($_GET['category']){
			$crud->form->getElement('item_id')->getModel()->addCondition('category_id',$_GET['category']);
		}

		if($crud->form){
			$category_field=$crud->form->getElement('category');
			$category_field->js('change',$crud->form->js()->atk4_form('reloadField','item_id',array($this->api->url(),'category'=>$category_field->js()->val())));      
		}
		
		if($crud->grid){
			$crud->grid->setFormatter('item','hindi');
			$crud->grid->addColumn('sno','Sno');
			$crud->grid->addOrder()->move('Sno','first')->now();
		}

		if($crud->form){
			$crud->form->getElement('item_id')->setAttr('class','hindi');
		}


	}
}