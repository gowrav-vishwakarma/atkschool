<?php
class page_store_mesh extends Page{
function page_index(){
	// parent::init();

	$this->add('H1')->set('Mesh Inward And Consume');
	$grid=$this->add('Grid');
	$party=$this->add('Model_Party');
	$party->addCondition('is_mesh_supplier',true);
	$grid->setModel($party);
	$grid->addQuickSearch(array('ename'));
	$grid->addColumn('expander','inward');
	$grid->addColumn('expander','inwardDetail');

	if($_GET['add_mesh_inward']){
		$this->js()->univ()->frameURL('Add Mesh Inward',$this->api->url('store_meshinward',array('party_id'=>$_GET['add_mesh_inward'])))->execute();
	}

}

function page_inward(){
	$this->api->stickyGET('party_master_id');
	$form=$this->add('Form');
	$mesh_inward=$this->add('Model_Mesh_ItemInward');
	$mesh_inward->addCondition('party_id',$_GET['party_master_id']);
	$mesh_inward->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
	$form->setModel($mesh_inward);
	$form->getElement('item_id')->addClass('hindi');
	$form->addSubmit('Inward');
	if($form->isSubmitted()){
		$form->update();
		$form->js(null,$form->js()->reload())->univ()->successMessage('Inward Successfully')->execute();
	}
}

function page_inwardDetail(){

	$form=$this->add('Form',null,null,array('form_horizontal'));
	$form->addField('DatePicker','from_date');
	$form->addField('DatePicker','to_date');
	$item_field=$form->addField('dropdown','item')->addClass('hindi')->setEmptyText('Please Select');
	$mesh_item=$this->add('Model_Mesh_Item');
	$item_field->setModel($mesh_item);
	$form->addSubmit('Filter');

	$this->api->stickyGET('party_master_id');
	$crud=$this->add('CRUD',array('allow_add'=>false));
	$mi=$this->add('Model_Mesh_ItemInward');
	$mi->addCondition('party_id',$_GET['party_master_id']);
	$mi->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
	$mi->_dsql()->order('id','desc');
	$mi->getElement('session_id')->system(true);

	if($_GET['filter']){
		if($_GET['from_date'])
			$mi->addCondition('date','>=',$_GET['from_date']);
		if($_GET['to_date'])
			$mi->addCondition('date','<=',$_GET['to_date']);
		if($_GET['item'])
			$mi->addCondition('item_id','<=',$_GET['item']);
	}
	$crud->setModel($mi);
	if($crud->grid){

	$crud->grid->addQuickSearch(array('item','party'));
	$crud->grid->addPaginator(10);
	$crud->grid->addFormatter('item','hindi');
	$crud->grid->addFormatter('party','hindi');
	// $crud->grid->removeColumn('session');
	}

	if($crud->form){
	$crud->form->getElement('item_id')->addClass('hindi');

	}

	if($form->isSubmitted()){
		$crud->grid->js()->reload(array('from_date'=>$form->get('from_date')?:0,'to_date'=>$form->get('to_date')?:0,'item'=>$form->get('item'),'filter'=>1))->execute();
	}
}

}