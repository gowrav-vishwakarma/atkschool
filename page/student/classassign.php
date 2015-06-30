<?php

class page_student_classassign extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('filter');
		$this->api->stickyGET('class');

		$form=$this->add('Form');
		$class_field=$form->addField('dropdown','class')->setEmptyText('----')->setAttr('class','hindi');
		$class_field->setModel('Class');
		$form->addSubmit('Search');

		$crud=$this->add('CRUD');
		$sc=$this->add('Model_Students_Current');
		// $sc->getElement('scholar_id')->destroy();
		// $sc->hasOne('Scholars_Unalloted','scholar_id');
		// $sc->_dsql()->del('order')->order('fname','asc');

		if($_GET['filter']){
			if($_GET['class']) $sc->addCondition('class_id',$_GET['class']);
		}else{
			$sc->addCondition('class_id',-1);
		}
		$crud->setModel($sc,array('scholar_id','class_id','ishostler','isScholared','bpl'),array('name','father_name','class','ishostler','isScholared','bpl'));
		if($crud->grid){
			$crud->grid->addFormatter('name','hindi');
			$crud->grid->addFormatter('father_name','hindi');
			$crud->grid->addFormatter('class','hindi');

			$crud->grid->addPaginator(10);
			
		}
		if($crud->form){
			$crud->form->getElement('scholar_id')->setAttr('class','hindi');
		}

		if($form->isSubmitted()){
			$crud->grid->js()->reload(array("class"=>$form->get('class'),
												"filter"=>1
												))->execute();
		}
	}
}