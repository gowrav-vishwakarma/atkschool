<?php
class page_student_feereciept extends Page{
	function init(){
		parent::init();
		$fname_array = array('fname');
		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('filter');
		$this->api->stickyGET('student');
		$this->api->stickyGET('class');
		$this->api->stickyGET('status');

		$class=$this->add('Model_Class');
		$student=$this->add('Model_Students_Current');
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$field_class=$form->addField('dropdown','class')->setEmptyText("---")->setAttr('class','hindi');

		$field_student=$form->addField('dropdown','student')->setEmptyText("---")->setAttr('class','hindi');


		$field_class->setModel($class);

		if($_GET['class_id']){
			$student->addCondition('class_id',$_GET['class_id']);
		}

		$field_class->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$field_class->js()->val())));

		$field_student->setModel($student);

		$form->addSubmit("Search");

		$grid=$this->add('Grid');
		$deposit=$this->add('Model_Fees_Deposit');
		$fee_applicable_j=$deposit->join('fee_applicable.id','fee_applicable_id');
		$fee_applicable_join_student=$fee_applicable_j->join('student.id','student_id');
		$fee_applicable_join_student->addField('student_id','id');
		$fee_applicable_join_student->addField('session_id');

		$fee_applicable_join_student->hasOne('Class','class_id');

		$fee_applicable_join_feeclassmapping=$fee_applicable_j->join('fee_class_mapping.id','fee_class_mapping_id');
		$fee_applicable_join_feeclassmapping_join_fee=$fee_applicable_join_feeclassmapping->join('fee.id','fee_id');
		
		$fee_applicable_feehead_j=$fee_applicable_join_feeclassmapping_join_fee->join('fee_heads.id','feehead_id');
		$fee_applicable_feehead_j->addField('Fee_Head','name');

		$deposit->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));

		$deposit->_dsql()->group('fee_applicable_id');
		
		if($_GET['filter']){
			if($_GET['class']) 
				$deposit->addCondition('class_id',$_GET['class']);
			if($_GET['student']){ 
				$deposit->addCondition('student_id',$_GET['student']);
				$fname_array=array();
			}
		}else{
			$deposit->addCondition('id',-1);
		}
		$deposit->tryLoadAny();


		$grid->setModel($deposit,array('Fee_Head','total_amount','deposit_date','xpaid','receipt_number'));
		$grid->addPaginator(10);
		$grid->addColumn('Button','print');
		
		if($_GET['print']){
			$deposit->addCondition('id',$_GET['print']);
			$deposit->tryLoadAny();
			// throw new Exception($deposit['Fee_Head'], 1);
			// var_dump($_GET['print']);
			$this->js()->univ()->newWindow($this->api->url('printfeesreceipt',array('class'=>$_GET['class'],
			'student'=>$_GET['student'],'date'=>$deposit['deposit_date'],'FeeHead'=>$deposit['Fee_Head'],'amount'=>$deposit['total_amount'],'receipt_no'=>$deposit['receipt_number'],'cut_page'=>0)))->execute();
		}
		

		if($form->isSubmitted()){
			$grid->js()->reload(array('class'=>$form->get('class'),
											'student'=>$form->get('student'),
											'status'=>$form->get('status'),
											'filter'=>1))
											->execute();
		}

		
	}

}