<?php

class page_masters_fee extends Page{
	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add('CRUD');
		$fees=$this->add('Model_Fee');
		// throw new Exception($session['id'], 1);
		
		$crud->setModel($fees);

		if($crud->grid){
		// $session=$this->add('Model_Sessions_Current');
		// $fees->addCondition('session_id',$session->id);
			$crud->grid->addColumn('expander','classassociation','ClassAssociation');
	}
}


	function page_classassociation(){

		$this->api->stickyGET('fee_id');

		$fee=$this->add('Model_Fee');
		$fee->load($_GET['fee_id']);
	
		$options=array(
				'leftModel' => $fee,
				'mappingModel' => 'FeeClassMapping',
				'leftField' => 'fee_id',
				'rightField' => 'class_id',
				'rightModel' => 'Class',
				'deleteFirst' => true,
				'maintainSession' => true,
				// 'field_other_then_id'=>'class_id'
			);		
		// $this->add('View')->set('Hi');
		$p=$this->add('View_Mapping',$options);
	}


	
}