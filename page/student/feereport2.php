<?php
class page_student_feereport2 extends Page{
	function init(){
		parent::init();

		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('filter');
		$this->api->stickyGET('class');
		$this->api->stickyGET('student');

		$fname_array = array('fname');
		
		// ===form matter
		$class=$this->add('Model_Class');
		$student=$this->add('Model_Student');

		$student->setOrder('fname','asc');


		$form=$this->add('Form',null,null,array('form_horizontal'));
		$field_class=$form->addField('dropdown','class')->setEmptyText("---")->setAttr('class','hindi');
		$field_student=$form->addField('dropdown','student')->setEmptyText("---")->setAttr('class','hindi');

		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');

		$field_class->setModel($class);
		if($_GET['class_id']){
			$student->addCondition('class_id',$_GET['class_id']);
		}
		$field_class->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$field_class->js()->val())));
		$field_student->setModel($student);

		$form->addSubmit("Search");


		//=== GRID MATTER

		$grid=$this->add('Grid');

		$fee_deposit=$this->add('Model_Fees_Deposit');
		$fd_join_fa=$fee_deposit->join('fee_applicable.id','fee_applicable_id');
		$fd_join_fa->addField('amount');
		$fd_join_fa->addField('student_id');

		$fd_join_fa_student=$fd_join_fa->join('student.id','student_id');
		$fd_join_fa_student->hasOne('Class','class_id');
		$scholar=$fd_join_fa_student->join('scholars_master.id','scholar_id');
		$scholar->addField('fname');

		

		if($_GET['filter']){
			if($_GET['class']) $fee_deposit->addCondition('class_id',$_GET['class']);
			if($_GET['student']){ 
				$fee_deposit->addCondition('student_id',$_GET['student']);
				$fname_array=array();
			}
			if($_GET['from_date']) $fee_deposit->addCondition('deposit_date','>=',$_GET['from_date']);
			if($_GET['to_date']) $fee_deposit->addCondition('deposit_date','<=',$_GET['to_date']);


		}
		else{
			$fee_deposit->addCondition('id',-1);
		}


		$grid->setModel($fee_deposit,array_merge($fname_array,array('FeeName','amount','deposit_date','paid','due')))	;
		$grid->addPaginator(10);


		if($form->isSubmitted()){
			$grid->js()->reload(array('class'=>$form->get('class'),
											'student'=>$form->get('student'),
											'status'=>$form->get('status'),
											'filter'=>1))
											->execute();
		}
	}

	// function page_details(){

	// 	$this->api->stickyGET('fee_applicable_id');

	// 	$fd=$this->add('Model_Fees_Deposit');
	// 	$fd->addCondition('fee_applicable_id',$_GET['fee_applicable_id']);
	// 	$fd->addExpression('fee_name')->set(function($m,$q){
	// 		$m1=$m->add('Model_Fees_Deposit');
	// 		$m1->table_alias='tempdeposit';
	// 		$fee_applicable = $m1->join('fee_applicable.id','fee_applicable_id');
	// 		$class_fee = $fee_applicable->join('fee_class_mapping.id','fee_class_mapping_id');
	// 		$fee = $class_fee->join('fee.id','fee_id');
	// 		$fee->addField('feename','name');
	// 		$m1->addCondition($m1->getField('id'),$q->getfield('id'));
	// 		return $m1->fieldQuery('feename');
	// 	});

	// 	$grid=$this->add('Grid');
	// 	$grid->setModel($fd,array('fee_name','paid','deposit_date','receipt_number','remarks'));

	// }
}