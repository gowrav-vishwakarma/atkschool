<?php
class page_store_outward extends Page{
	function init(){
		parent::init();
		$item_consume=$this->add('Model_Consume');
		$cols=$this->add('Columns');
		$col1=$cols->addColumn(6);
		$col2=$cols->addColumn(6);
		
		$form=$col1->add('Form');
		$category_field=$form->addField('dropdown','category');
		$category_field->addClass('hindi');
		$category_field->setModel('Model_Item_Category');
		$item_consume->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$form->setModel($item_consume);
		if($_GET['category_id']){
			$form->getElement('item_id')->getModel()->addCondition('category_id',$_GET['category_id']);			
		}

		$form->getElement('item_id')->addClass('hindi');
		$form->addSubmit('Consume');	

		$filter_form=$this->add('Form',null,null,array('form_horizontal'));
		$filter_form->addField('DatePicker','from_date');
		$filter_form->addField('DatePicker','to_date');
		$item_field=$filter_form->addField('dropdown','item')->addClass('hindi');
		$item_field->setModel('Item');
		$filter_form->addSubmit('Filter');

		$crud=$this->add('CRUD',array('allow_add'=>false));
		$item_consume->_dsql()->order('id','desc');

		if($_GET['filter']){
			if($_GET['from_date'])
				$item_consume->addCondition('date','>=',$_GET['from_date']);
			if($_GET['to_date'])
				$item_consume->addCondition('date','<=',$_GET['to_date']);
			if($_GET['item'])
				$item_consume->addCondition('item_id',$_GET['item']);
		}
		$crud->setModel($item_consume,array('item','quantity','remarks','unit','date'));
					
		if($crud->grid){
			$crud->grid->addQuickSearch(array('item','unit','quantity'));
			$crud->grid->addPaginator(10);
			$crud->grid->addFormatter('item','hindi');
			// $crud->grid->addFormatter('remarks','hindi');
		}
			
		$category_field->js('change',$form->js()->atk4_form('reloadField','item_id',array($this->api->url(),'category_id'=>$category_field->js()->val())));

		if($filter_form->isSubmitted()){
			$crud->grid->js()->reload(array('from_date'=>$filter_form->get('from_date')?:0,'to_date'=>$filter_form->get('to_date')?:0,'item'=>$filter_form->get('item'),'filter'=>1))->execute();
		}
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,array($form->js()->reload(),$crud->js()->reload()))->univ()->successMessage('Consume Successfully')->closeDialog()->execute();
		}

	}
}