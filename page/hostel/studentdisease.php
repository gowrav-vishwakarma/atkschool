<?php
class page_hostel_studentdisease extends Page{
	function page_index(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form');
		$form->addField('dropdown','treatment','Filter For')->setValueList(array('all'=>'All', 'nt'=>'Not Treated','t'=>'Treated'));
		$form->addSubmit('Filter');

		$crud=$this->add('CRUD');

		
		$m=$this->add('Model_Students_Disease');
		if($_GET['filter']){
			switch ($_GET['filter']) {
				case 'nt':
					$temp=0;
					break;
				case 't':
					$temp=1;
					break;
				default:
					$temp = false;
					break;
			}
			if($temp !== false)	$m->addCondition('treatment',$temp);
		}
		// else{
		// 	$m->addCondition('treatment',-1);
		// }

		
		$m->_dsql()->order('treatment_date','desc');
		$crud->setModel($m,array('class','student_id','disease_id','treatment','treatment_date','fname'),array('student','disease','report_date','treatment','treatment_date','fname'));
		if($crud->grid){
			
			if($form->isSubmitted()){
				$crud->grid->js()->reload(array('filter'=>$form->get('treatment')))->execute();
			}

			$crud->grid->setFormatter('student','hindi');
			$crud->grid->setFormatter('disease','hindi');
			$crud->grid->addColumn('Expander','addtreatment','Add Treatment');

		}
		if($crud->form){
			$c=$this->add('Model_Class');
			$crud->form->getElement('student_id')->setAttr('class','hindi');
			$crud->form->getElement('disease_id')->setAttr('class','hindi');
			$class_field=$crud->form->addField('dropdown','class')->setEmptyText("---")->setAttr('class','hindi');
			$class_field->setModel($c);
			if($_GET['class_idx']){
				$crud->form->getElement('student_id')->dq->where('class_id',$_GET['class_idx'])->order('fname');
			}
			// throw $this->Exception("Error Processing Request",$_GET['class_idx']);
			
			$class_field->js('change',$crud->form->js()->atk4_form('reloadField','student_id',array($this->api->getDestinationURL(), 'class_idx'=>$class_field->js()->val())));
		
			$crud->form->add('Order')->move('class','before','student_id')->now();
		}
	}

	function page_addtreatment(){
		$this->api->stickyGET('disease_master_id');
		$dr=$this->add('Model_Disease_Remarks');
		$dr->addCondition('disease_id',$_GET['disease_master_id']);
		// $dr->load($_GET['disease_master_id']);

		$crud=$this->add('CRUD');
		$crud->setModel($dr,array('remarks'),array('remarks','created_at'));
		if($crud->form) {
					$crud->form->getElement('remarks')->setAttr('class','hindi');
				}
		if($crud->grid) $crud->grid->addFormatter('remarks','hindi');
	}
}