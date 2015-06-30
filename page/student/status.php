<?php
class page_student_status extends Page{
	function init(){
		parent::init();
		$this->api->stickyGET('student');
		$acl=$this->add('xavoc_acl/Acl');
		
		$c=$this->add('Model_Class');
		$s=$this->add('Model_Students_Current');
		// $s->debug();



		$form=$this->add('Form',null,null,array('form_horizontal'));
		$field_class=$form->addField('dropdown','class')->setEmptyText('----')->setAttr('class','hindi');

		$field_class->setModel($c);

		$field_student=$form->addField('dropdown','student')->setEmptyText('----')->setAttr('class','hindi');;

		if($_GET['class_id']){
			$s->addCondition('class_id',$_GET['class_id']);
		}

		$field_student->setModel($s);


		$form->addField('dropdown','status')->setValueList(array('due'=>"Due",
																'paid'=>"Paid"))->setEmptyText("Select Any Status");

		$form->addField('DatePicker','from_date');
		$form->addField('DatePicker','to_date');


		$field_class->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$field_class->js()->val())));
		
		$form->addSubmit('Get List');


		$grid=$this->add('Grid');

		$fees_deposite=$this->add('Model_Fees_Deposit');
		$fees_deposite_join_fees_Applicable=$fees_deposite->join('fee_applicable.id','fee_applicable_id');
		$fees_deposite_join_fees_Applicable_join_student=$fees_deposite_join_fees_Applicable->join('student.id','student_id');

		$fees_deposite_join_fees_Applicable->addField('student_id');
		
		// $fees_deposite_join_fees_Applicable_join_student=$fees_deposite_join_fees_Applicable->join('student.id','student_id');
		$scholar=$fees_deposite_join_fees_Applicable_join_student->join('scholars_master.id','scholar_id');
		$scholar->addField('hname');
		$scholar->addField('fname');
		$fees_deposite_join_fees_Applicable_join_student->hasOne('Class','class_id');
		$fees_deposite_join_fees_Applicable->addField('amount');
		$fees_deposite_join_fees_Applicable->addField('due');
		// $fees_deposite->addExpresstion('Paid')->set('amount-due');
		// $fees_deposite->debug();

		if($_GET['filter']){
			if($_GET['class']) $fees_deposite->addCondition('class_id',$_GET['class']);

			if($_GET['student']) $fees_deposite->addCondition('student_id',$_GET['student']);

			if($_GET['status']){
				if($_GET['status'] == 'paid'){
					$fees_deposite->addCondition('due', 0);
					
				}
				
				if($_GET['status'] == 'due')
					$fees_deposite->addCondition(,'<>',0);
				
			}

			if($_GET['from_date']) $fees_deposite->addCondition('deposit_date','>=',$_GET['from_date']);
			
			if($_GET['to_date']) $fees_deposite->addCondition('deposit_date','<=',$_GET['to_date']); 

		}


		$grid->setModel($fees_deposite,array('fname','amount','paid','deposit_date','hname'));
		// $grid->getElement('hname')->addFormatter('class','hindi');


		if($form->isSubmitted()){
			$grid->js()->reload(array('class'=>$form->get('class'),
										'student'=>$form->get('student'),
										'status'=>$form->get('status'),
										'from_date'=>$form->get('from_date'),
										'to_date'=>$form->get('to_date'),
										'filter'=>1))->execute();
		}
	}
}


