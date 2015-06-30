<?php
class page_student_contact extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form');
		$class_field=$form->addField('dropdown','class')->setEmptyText('----')->setAttr('class','hindi');
		$class_field->setModel('Class');
		$form->addSubmit('GetList');



		$grid=$this->add('Grid');
		$sc=$this->add('Model_Scholars_Current');

		$grid->addColumn('sno','sno');

		if($_GET['filter']){
			if($_GET['class']) $sc->addCondition('class_id',$_GET['class']);
		 }
		 else{
			$sc->addcondition('class_id',-1);
		}
		// $grid->addPaginator();
		$sc->_dsql()->del('order')->order('fname');
		$grid->setModel($sc,array('sno','fname','hname','father_name','class','contact'));
		$grid->setFormatter('class','hindi');

		if($form->isSubmitted()){
			$grid->js()->reload(array("class"=>$form->get('class'),
										"filter"=>1))->execute();
		}
	}
}
