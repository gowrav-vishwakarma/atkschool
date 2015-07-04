<?php
class page_store_meshconsume extends Page{
	function init(){
		parent::init();
		$cols=$this->add('Columns');
		$col1=$cols->addColumn(6);
		$col2=$cols->addColumn(6);
		$this->api->stickyGET('party_id');
		$col1->add('H1')->setHTML('<h2>Consume Mess Items Here</h2>');
		$form=$col1->add('Form');
		$item_consume=$this->add('Model_Mesh_ItemConsume');
		$item_consume->addCondition('party_id',$_GET['party_id']);
		$item_consume->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$form->setModel($item_consume);
		$form->getElement('item_id')->addClass('hindi');
		$form->addSubmit('Consume');
		
		// $filter_form=$col1->add('Form',null,null,array('form_horizontal'));
		// $item_field=$filter_form->addField('dropdown','items');
		// $item_field->setModel($this->add('Model_Mesh_ItemInward')->ref('item_id'));
		// $filter_form->addField('DatePicker','from_date');
		// $filter_form->addField('DatePicker','to_date');
		// $filter_form->getElement('items')->addClass('hindi');
		// $filter_form->addSubmit('Filter');

		$filter_form=$this->add('Form',null,null,array('form_horizontal'));
		$filter_form->addField('DatePicker','from_date');
		$filter_form->addField('DatePicker','to_date');
		$item_field=$filter_form->addField('dropdown','item')->addClass('hindi')->setEmptyText('Please Select');
		$item_field->setModel('Model_Mesh_Item');
		$filter_form->addSubmit('Filter');


		$crud=$this->add('CRUD',array('allow_add'=>false));
		$item_consume->_dsql()->order('id','desc');
		if($_GET['filter']){
			if($_GET['item'])
				$item_consume->addCondition('item_id',$_GET['item']);
			if($_GET['from_date'])
				$item_consume->addCondition('date','>=',$_GET['from_date']);
			if($_GET['to_date'])
				$item_consume->addCondition('date','<=',$_GET['to_date']);
		}
		$crud->setModel($item_consume);
		if($filter_form->isSubmitted()){
			$crud->grid->js()->reload(array('from_date'=>$filter_form->get('from_date'),'to_date'=>$filter_form->get('to_date'),'item'=>$filter_form->get('item'),'filter'=>1))->execute();
		}
		if($crud->grid){
		$crud->grid->addQuickSearch(array('item','unit','quantity'));
		$crud->grid->addPaginator(15);
		$crud->grid->addFormatter('item','hindi');
		$crud->grid->removeColumn('party');
		$crud->grid->removeColumn('session');
		}

		if($crud->form){
			$crud->form->getElement('item_id')->addClass('hindi');
		}
			
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,array($form->js()->reload(),$crud->js()->reload()))->univ()->successMessage('Consume Successfully')->closeDialog()->execute();
		}

		// if($filter_form->isSubmitted()){
		// 	$crud->grid->js()->reload(array('item'=>$form->get('items'),
		// 									'from_date'=>$form->get('from_date')?:null,
		// 									'to_date'=>$form->get('to_date')?:null,
		// 									'filter'=>1))->execute();
		// }
	}
}