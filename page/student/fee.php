<?php
class page_student_fee extends Page{
	function page_index(){
		// parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$c=$this->add('Model_Class');
		$s=$this->add('Model_Students_Current');
		$sc=$this->add('Model_Students_Current');
		try{
			// $form->api->db->beginTransaction();
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$field_class=$form->addField('dropdown','class')->setEmptyText('----')->setAttr('class','hindi');
		$field_class->setModel($c);

		if($_GET['class_id']){
			$s->addCondition('class_id',$_GET['class_id']);
		}

		$field_student=$form->addField('dropdown','student')->setEmptyText('----')->setAttr('class','hindi');
		$s->setOrder('fname','asc');
		$field_student->setModel($s);

		$field_class->js('change',$form->js()->atk4_form('reloadField','student',array($this->api->url(),'class_id'=>$field_class->js()->val())));
		$form->addSubmit('GetList');

		$grid=$this->add('Grid');


		if($_GET['filter']){
			if($_GET['class']) $sc->addCondition('class_id',$_GET['class']);
			if($_GET['student']) $sc->addCondition('id',$_GET['student']);
		}else{
			$sc->addCondition('class_id',-1);
		}
		$grid->addColumn('sno','sno');
		$grid->setModel($sc,array('sno','name','fname','father_name','ishostler','isScholared'));
		$grid->addColumn('Expander','deposit','Fee Deposit');
		$grid->addFormatter('father_name','hindi');

		if($form->isSubmitted()){
			$grid->js()->reload(array("class"=>$form->get('class'),
										"student"=>$form->get('student'),
										"filter"=>1))->execute();

		}
		}catch(Exception $e){
			$this->js()->univ()->errorMessage($e->getMessage())->execute();

	}
}
	
	function page_deposit(){

		$this->api->stickyGET('student_id');
		$fa=$this->add('Model_Fees_Applicable');
		$fa->addCondition('student_id',$_GET['student_id']);

		
		$this->add('Button','add_fast_fee')->setLabel('Very Fast Deposit')->js('click',$this->js()->univ()->frameURL('Fee Deposit (Very Fast method)',$this->api->url('./new_fast',array('student_id'=>$_GET['student_id']))));
		$this->add('Button','add_fee')->setLabel('Fast Deposit')->js('click',$this->js()->univ()->frameURL('Fee Deposit (Fast/auto method)',$this->api->url('./new',array('student_id'=>$_GET['student_id']))));
		$this->add('Button','add_fee_detail')->setLabel('Detailed Deposit')->js('click',$this->js()->univ()->frameURL('Fee Deposit (Detailed)',$this->api->url('./new_detailed',array('student_id'=>$_GET['student_id']))));
		$this->add('Button','fee_manage')->setLabel('Manage Deposit Fees')->js('click',$this->js()->univ()->frameURL('Manage Deposit Fees',$this->api->url('./manage_deposit',array('student_id'=>$_GET['student_id']))));
		$crud=$this->add('CRUD',array('allow_add'=>false,'allow_del'=>false));
		
		$crud->setModel($fa,array('fee_class_mapping','amount','paid','due','remarks'));
		
		if($crud->grid){
			$crud->grid->setFormatter('amount','number');
			$crud->grid->setFormatter('paid','number');
			$crud->grid->setFormatter('due','number');
			$crud->grid->addClass('fee_applicable');
			$crud->grid->addTotals(array('amount','paid','due'));
			$crud->grid->js('reload',$crud->grid->js()->reload());
		}


	}

	function page_deposit_new_fast(){
		$this->api->stickyGET('student_id');
		$fa=$this->add('Model_Fees_Applicable');
		$fa->addCondition('student_id',$_GET['student_id']);
		$total_due_fee = $fa->sum('due')->getOne();
		
		$this->add('View_Info')->set("Total Due Amount :".$total_due_fee);
		$form = $this->add('Form');

		$form->addField('line','amount_submitted')->setNotNull();
		// $form->addField('line','due_amount');
		// $form->addField('text','remarks');
		$form->addField('line','receipt_number');
		$form->addField('text','remarks');
		$form->addField('DatePicker','submitted_on')->set(date('Y-m-d'));
		$form->addSubmit('Receive');

		if($form->isSubmitted()){
			try{			
				$form->api->db->beginTransaction();
				$student=$this->add('Model_Student');
				$student->load($_GET['student_id']);
				$class_id=$student['class_id'];

				// $form->displayError('receipt_number',$form->get('receipt_number'));

				$fee_head=$this->add('Model_FeesHead');
				// $fee_head->load($form->get('fee_head'));

				$fee=$this->add('Model_Fee');
				
				$amount_to_adjust=$form->get('amount_submitted');

				foreach($fee_head as $fee_head_junk){
					foreach($fee=$fee_head->ref('Fee') as $fee_junk){
						if($amount_to_adjust==0) break;
						$fee_class_map=$this->add('Model_FeeClassMapping');
						$fee_class_map->addCondition('fee_id',$fee->id);
						$fee_class_map->addCondition('class_id',$class_id);
						$fee_class_map->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
						$fee_class_map->tryLoadAny();
						if(!$fee_class_map->loaded()) continue;

						$fee_app=$this->add("Model_Fees_Applicable");
						$fee_app->addCondition('fee_class_mapping_id',$fee_class_map->id);
						$fee_app->addCondition('student_id',$student->id);
						// $fee_app->debug();
						$fee_app->tryLoadAny();
						if(!$fee_class_map->loaded()) throw $this->exception("Somthing done Wrong with this entry, Of particluar student");

						$amount_for_this_fee = ($fee_app['due'] >= $amount_to_adjust)? $amount_to_adjust: $fee_app['due'];
						// throw $this->exception("Paid ". $fee_app['paid']);

						// Add deposite row
						// substract from this feeapp due 
						// recalculate 

						if($amount_for_this_fee == 0 ) continue;

						$fee_deposit=$this->add('Model_Fees_Deposit');
						$fee_deposit['paid']=$amount_for_this_fee;
						$fee_deposit['receipt_number']=$form->get('receipt_number');
						$fee_deposit['remarks']=$form->get('remarks');
						$fee_deposit['deposit_date']=$form->get('submitted_on');
						$fee_deposit['fee_applicable_id']=$fee_app->id;
						$fee_deposit->save();

						// $fee_app['due'] = $fee_app['due'] - $amount_for_this_fee;
						// $fee_app->save();

						$amount_to_adjust = $amount_to_adjust - $amount_for_this_fee;

					}
				}
				if($amount_to_adjust > 0 ) throw $this->exception('Exxcess fee deposited '.$amount_to_adjust);
			}catch(Exception $e){
					$form->api->db->rollback();
					$form->js()->univ()->errorMessage($e->getMessage())->execute();
					throw $e;
			}
			$form->api->db->commit();
			$form->js(null,
					$form->js()->_selector('.fee_applicable')->trigger('reload')
				)->univ()->closeDialog()->execute();
			$form->js(null,$this->js()->reload())
			->univ()
			->successMessage("Student Record Upadated success fully ")
			->execute();
		}


	} 
	


	function page_deposit_new(){
		$this->api->stickyGET('student_id');
		
		$form = $this->add('Form');

		$f=$this->add('Model_FeesHead');
		$field_fee=$form->addField('dropdown','fee_head')->setEmptyText("----");
		$field_fee->setModel($f);
		$form->addField('line','amount_submitted')->setNotNull();
		$field_amount=$form->addField('line','amount_due');
		if($_GET['feehead_id']){
		$fa=$this->add('Model_Fees_Applicable');
		$fa->addCondition('student_id',$_GET['student_id']);
		$fa->addCondition('feehead_id',$_GET['feehead_id']);
		$total_due_fee = $fa->sum('due')->getOne();
		$field_amount->set($total_due_fee);	
		}



		// $form->addField('line','due_amount');
		// $form->addField('text','remarks');
		$form->addField('line','receipt_number');
		$form->addField('text','remarks');
		$form->addField('DatePicker','submitted_on')->set(date('Y-m-d'));
		$form->addSubmit('Receive');
		$field_fee->js('change',$form->js()->atk4_form('reloadField','amount_due',array($this->api->url(),'feehead_id'=>$field_fee->js()->val())));	
		if($form->isSubmitted()){
			try{			
				$form->api->db->beginTransaction();
				$student=$this->add('Model_Student');
				$student->load($_GET['student_id']);
				$class_id=$student['class_id'];

				$fee_head=$this->add('Model_FeesHead');
				$fee_head->load($form->get('fee_head'));
				
				$amount_to_adjust=$form->get('amount_submitted');


				foreach($fee=$fee_head->ref('Fee') as $fee_junk){
					if($amount_to_adjust==0) break;
					$fee_class_map=$this->add('Model_FeeClassMapping');
					$fee_class_map->addCondition('fee_id',$fee->id);
					$fee_class_map->addCondition('class_id',$class_id);
					$fee_class_map->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
					$fee_class_map->tryLoadAny();
					if(!$fee_class_map->loaded()) continue;

					$fee_app=$this->add("Model_Fees_Applicable");
					$fee_app->addCondition('fee_class_mapping_id',$fee_class_map->id);
					$fee_app->addCondition('student_id',$student->id);
					// $fee_app->debug();
					$fee_app->tryLoadAny();
					if(!$fee_class_map->loaded()) throw $this->exception("Somthing done Wrong with this entry, Of particluar student");

					$amount_for_this_fee = ($fee_app['due'] >= $amount_to_adjust)? $amount_to_adjust: $fee_app['due'];
					// throw $this->exception("Paid ". $fee_app['paid']);

					// Add deposite row
					// substract from this feeapp due 
					// recalculate 

					if($amount_for_this_fee == 0 ) continue;

					$fee_deposit=$this->add('Model_Fees_Deposit');
					$fee_deposit['paid']=$amount_for_this_fee;
					$fee_deposit['receipt_number']=$form->get('receipt_number');
					$fee_deposit['remarks']=$form->get('remarks');
					$fee_deposit['deposit_date']=$form->get('submitted_on');
					$fee_deposit['fee_applicable_id']=$fee_app->id;
					$fee_deposit->save();

					// $fee_app['due'] = $fee_app['due'] - $amount_for_this_fee;
					// $fee_app->save();

					$amount_to_adjust = $amount_to_adjust - $amount_for_this_fee;

				}

				if($amount_to_adjust > 0 ) throw $this->exception('Exxcess fee deposited '.$amount_to_adjust);
			}catch(Exception $e){
					$form->api->db->rollback();
					$form->js()->univ()->errorMessage($e->getMessage())->execute();
					throw $e;
			}
			$form->api->db->commit();
			$form->js(null,
					$form->js()->_selector('.fee_applicable')->trigger('reload')
				)->univ()->closeDialog()->execute();
			$form->js(null,$this->js()->reload())
			->univ()
			->successMessage("Student Record Upadated success fully ")
			->execute();
		}


	} 


	function page_deposit_new_detailed(){
		
		$this->api->stickyGET('student_id');

		$form=$this->add('Form');

		$feehead_field=$form->addField('dropdown','fees_head')->setEmptyText('-----');

		$fees_field=$form->addField('dropdown','fees')->setEmptyText('-----');

		$form->addField('line','amount_submit')->setNotNull();
		$form->addField('line','receipt_number');
		$form->addField('text','remarks');
		$form->addField('DatePicker','submitted_on')->set(date('Y-m-d'));
		$form->addSubmit('Receive');



		$feehead=$this->add('Model_FeesHead');
		$fee=$this->add('Model_Fee');

		if($_GET['feehead_id']){
			$fee->addCondition('feehead_id',$_GET['feehead_id']);
		}

		$feehead_field->setModel($feehead);
		$fees_field->setModel($fee);

		// $crud=$this->add('CRUD',array("allow_add"=>false));

		// $fs=$this->add("Model_Fees_Deposit");
		

		// $crud->setModel($fs);

		$feehead_field->js('change',$form->js()->atk4_form('reloadField','fees',array($this->api->url(),'feehead_id'=>$feehead_field->js()->val())));	

		if($form->isSubmitted()){
			try{

				
				$form->api->db->beginTransaction();
				$student=$this->add('Model_Student');
				$student->load($_GET['student_id']);
				$class_id=$student['class_id'];

				$f=$this->add('Model_Fee');
				$f->load($form->get('fees'));

				$amount_to_submit=$form->get('amount_submit');


				foreach($fee as $fee_junk){
					if($amount_to_submit==0) break;
					$fee_class_map=$this->add('Model_FeeClassMapping');
					$fee_class_map->addCondition('fee_id',$fee->id);
					$fee_class_map->addCondition('class_id',$class_id);
					$fee_class_map->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
					$fee_class_map->tryLoadAny();
					if(!$fee_class_map->loaded()) continue;


					$fee_app=$this->add("Model_Fees_Applicable");
					$fee_app->addCondition('fee_class_mapping_id',$fee_class_map->id);
					$fee_app->addCondition('student_id',$student->id);
					$fee_app->tryLoadAny();
					if(!$fee_class_map->loaded()) throw $this->exception("Somthing done Wrong with this entry, Of particluar student");

					$amount_for_this_fee= ($fee_app['due'] >= $amount_to_submit)? $amount_to_submit: $fee_app['due'];
					// Add deposite row
					// substract from this feeapp due 
					// recalculate 
					if($amount_for_this_fee != 0 ) {
						$fee_deposit=$this->add('Model_Fees_Deposit');
						$fee_deposit['paid']=$amount_for_this_fee;
						$fee_deposit['receipt_number']=$form->get('receipt_number');
						$fee_deposit['remarks']=$form->get('remarks');
						$fee_deposit['deposit_date']=$form->get('submitted_on');
						$fee_deposit['fee_applicable_id']=$fee_app->id;
						$fee_deposit->save();

						// $fee_app['due'] = $fee_app['due'] - $amount_for_this_fee;
						// $fee_app->save();
					}
					$amount_to_submit = $amount_to_submit - $amount_for_this_fee;


				}
				if($amount_to_submit > 0 ) throw $this->exception('Exxcess fee deposited');
			}catch(Exception $e){
					$form->api->db->rollback();
					$form->js()->univ()->errorMessage($e->getMessage())->execute();
					throw $e;
			}
			$form->api->db->commit();
			// $form->js(null,$this->js()->reload())->univ()->successMessage("Student Record Upadated success fully ")->execute();
			$form->js(null,
					$form->js()->_selector('.fee_applicable')->trigger('reload')
				)->univ()->closeDialog()->execute();
			$form->js(null,$this->js()->reload())
			->univ()
			->successMessage("Student Record Upadated success fully ")
			->execute();

		}

	}

	function page_deposit_manage_deposit(){
		$this->api->stickyGET('student_id');

		$fee_applicable=$this->add('Model_Fees_Applicable');
		$fee_applicable->addCondition('student_id',$_GET['student_id']);

		$fee_applicable_array=array();

		foreach ($fee_applicable as $fee_applicable_junk) {
			$fee_applicable_array[]=$fee_applicable->id;
		}

		// echo "<pre>";
		// print_r($fee_applicable_array);
		// echo "</pre>";

		$crud=$this->add('CRUD',array('allow_add'=>false));
		$fd=$this->add('Model_Fees_Deposit');
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

		$fd->join('fee_applicable.id','fee_applicable_id')
			->addField('student_id');

		$fd->addCondition('student_id',$_GET['student_id']);

		// $fd->addCondition('fee_applicable_id','in',$fee_applicable_array);
		// $fd->debug();
		$crud->setModel($fd,array('paid','deposit_date','receipt_number','remarks'),array('fee_name','paid','deposit_date','receipt_number','remarks'));
	}
}