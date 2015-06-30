<?php
class page_student_feereport extends Page{
	function page_index(){
		// parent::init();

		$fname_array = array('fname');
		
		$class=$this->add('Model_Class');
		$student=$this->add('Model_Students_Current');

		$student->setOrder('fname','asc');


		$form=$this->add('Form',null,null,array('form_horizontal'));
		$field_class=$form->addField('dropdown','class')->setEmptyText("---")->setAttr('class','hindi');

		$field_student=$form->addField('dropdown','student')->setEmptyText("---")->setAttr('class','hindi');

		$form->addField('dropdown','status')->setValueList(array('due'=>'Due',
																'paid'=>'Paid'))->setEmptyText("Select Any");

		$field_class->setModel($class);
		if($_GET['class_id']){
			$student->addCondition('class_id',$_GET['class_id']);
		}
		$field_class->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$field_class->js()->val())));
		$field_student->setModel($student);

		$form->addSubmit("Search");

		$grid=$this->add('Grid');

		$fee_applicable=$this->add('Model_Fees_Applicable');
		$fee_applicable_join_student=$fee_applicable->join('student.id','student_id');
		$fee_applicable_join_student->hasOne('Class','class_id');
		// $fee_applicable_join_student->hasOne('Student','student_id');
		$scholar=$fee_applicable_join_student->join('scholars_master.id','scholar_id');
		$scholar->addField('fname');
		// $scholar->addField('amount');

		$fee_applicable_join_feeclassmapping=$fee_applicable->join('fee_class_mapping.id','fee_class_mapping_id');
		$fee_applicable_join_feeclassmapping_join_fee=$fee_applicable_join_feeclassmapping->join('fee.id','fee_id');
		$fee_applicable_join_feeclassmapping_join_fee->addField('FeeName','name');
		if($_GET['filter']){
			if($_GET['class']) $fee_applicable->addCondition('class_id',$_GET['class']);
			if($_GET['student']){ 
				$fee_applicable->addCondition('student_id',$_GET['student']);
				$fname_array=array();
			}
			if($_GET['status']){
				if($_GET['status']=='paid') $fee_applicable->addCondition('due',0);
				if($_GET['status']=='due') $fee_applicable->addCondition('due','<>',0);
			}
		}else{
			$fee_applicable->addCondition('id',-1);
		}


		$grid->setModel($fee_applicable,array_merge($fname_array,array('FeeName','amount','paid','due')))	;
		$grid->addPaginator(10);

		$grid->addColumn('expander','details','Deposite Details');

		if($form->isSubmitted()){
			$grid->js()->reload(array('class'=>$form->get('class'),
											'student'=>$form->get('student'),
											'status'=>$form->get('status'),
											'filter'=>1))
											->execute();
		}
	}

	function page_details(){

		$this->api->stickyGET('fee_applicable_id');

		$fd=$this->add('Model_Fees_Deposit');
		$fd->addCondition('fee_applicable_id',$_GET['fee_applicable_id']);
		$fd->addExpression('fee_name')->set(function($m,$q){
			$m1=$m->add('Model_Fees_Deposit');
			$m1->table_alias='tempdeposit';
			$fee_applicable = $m1->join('fee_applicable.id','fee_applicable_id');
			$class_fee = $fee_applicable->join('fee_class_mapping.id','fee_class_mapping_id');
			$fee = $class_fee->join('fee.id','fee_id');
			$fee->addField('feename','name');
			$m1->addCondition($m1->getField('id'),$q->getfield('id'));
			return $m1->fieldQuery('feename');
		});

		$grid=$this->add('Grid');
		$grid->setModel($fd,array('fee_name','paid','deposit_date','receipt_number','remarks'));

	}
}