<?php
class page_student_rollnoallotment extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('class');
		$this->memorize('class',$_GET['class']);

		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setEmptyText('-----')->set($this->recall('class',null));
		$class_field->setModel('Class');
		$class_field->setAttr('class','hindi');
		$roll_field=$form->addField('line','roll_no')->setNotNull();

		$form->addSubmit("Allot");

		$c=$this->add('Model_Students_Current');
		$c->_dsql()->del('order')->order('roll_no','asc')->order('fname','asc');
		$crud=$this->add('CRUD',array('allow_add'=>false,"allow_del"=>false,"allow_edit"=>false));
		
		if($this->recall('class',0)){
			$c->addCondition('class_id',$this->recall('class'));
		}else{
			
			$c->addCondition('class_id',-1);

		}

			$crud->setModel($c, array('fname','name','father_name','class','roll_no'));
		if($crud->grid){
			$grid= $crud->grid;
			// $grid->addColumn('Expander','edit','Edit');
			$grid->addClass('reladable_grid');
			$grid->addFormatter('class','hindi');
			$grid->addFormatter('father_name','hindi');
			$grid->addFormatter('roll_no','grid/inline');
			$grid->js('reloadme',$grid->js()->reload());
			// $crud->grid->addPaginator();
			// $grid->addQuicksearch('roll_no');
			$class_field->js('change',$crud->grid->js()->reload(array('class'=>$class_field->js()->val())));

		}
		if($form->isSubmitted()){

			$students=$this->add('Model_Students_Current');
			$students->addCondition('class_id',$form->get('class'));
			$students->_dsql()->del('order')->order('fname','asc');
			$start_roll_no=$form->get('roll_no');
			foreach ($students as $junk) {
				$students['roll_no'] = $start_roll_no ++;
				$students->save();
			}
			$crud->grid->js(null,$form->js()->reload())->reload(array("class"=>$form->get("class")))->execute();
		}

		


	}

	// function page_edit(){
	// 	$this->api->stickyGET('student_id');
	// 	$m=$this->add('Model_Students_Current');
	// 	$m->load($_GET['student_id']);
	// 	$form = $this->add('Form');
	// 	$form->setModel($m,array('roll_no'));
	// 	if($form->isSubmitted()){
	// 		$form->update();
	// 		$form->js()->univ()->successMessage('Upadetd')->closeExpander()->execute();
	// 		// $form->js()->_selector('.reladable_grid')->reload(array('class'=>$m['class_id']))->execute();
	// 	}
	// }
}