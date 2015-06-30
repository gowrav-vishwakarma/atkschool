<?php
class page_staff_movement_record extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$crud=$this->add('CRUD',array('allow_add'=>false,'allow_edit'=>false));
		// $grid=$this->add('Grid');
		$filter_field=$form->addField('dropdown','filter_duty')->setValueList(array('h'=>'Hostel','s'=>'School','0'=>'All'))->set('0');
		$staff_field=$form->addField('dropdown','staff')->setEmptyText('---')->setNotNull()->setAttr('class','hindi');

		$form->addSubmit("Filter");

		$sm= $this->add('Model_Staff');
		if($_GET['filter_duty']){
			$sm->addCondition('ofhostel','like',($_GET['filter_duty']=='h')? '1':'0');
		}

		$staff_field->setModel($sm);
		$filter_field->js('change',$form->js()->atk4_form('reloadField','staff',array($this->api->getDestinationURL(), 'filter_duty'=>$filter_field->js()->val())));



		$sm=$this->add('Model_Staff_Movement');

		$sm=$this->add('Model_Staff_Movement');
		$sm->addExpression('ename')->set(function($m,$q){
			return $m->refSQL('staff_id')->fieldQuery('ename');
		});


		$sm->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		// $sm->debug();
		$crud->setModel($sm,array('staff','ename','date','action'));
		if($form->isSubmitted()){
			$crud->grid->js()->reload(array('staff_id'=>$form->get('staff')))->execute();
		}

		if($_GET['staff_id']) $sm->addCondition('staff_id',$_GET['staff_id']);

		if($crud->grid){
			$crud->grid->dq->order('id','desc');
			$crud->grid->addFormatter('staff','hindi');
			$crud->grid->addPaginator();
		}

		if($crud->form){
			$crud->form->getElement('staff_id')->setAttr('class','hindi');
		}


	}
}