<?php

class page_store_itemoutward extends Page {
	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form');
		$grid=$this->add('Grid');
		$form->addField('line','store_no')->setNotNull();
		$month_field=$form->addField('dropdown','for_month')->setValueList(array("-1"=>"----",
																	"1"=>"jan",
																	"2"=>"Feb",
																	"3"=>"March",
																	"4"=>"April",
																	"5"=>"May",
																	"6"=>"Jun",
																	"7"=>"July",
																	"8"=>"Aguset",
																	"9"=>"Sep",
																	"10"=>"Oct",
																	"11"=>"Nov",
																	"12"=>"Dec"));
		$form->addSubmit('Get Details');

		$month_field->js('change',$grid->js()->reload());

		$m=$this->add('Model_Hosteler');
		$m->addCondition('session_id',$this->add('Model_Sessions_Current')->fieldQuery('id'));
		if($_GET['store_no']){
			$m->addCondition('store_no',$_GET['store_no']);
		}else{
			$m->addCondition('id',-1);
		}
		$grid->setModel($m,array('name','class'));
		$grid->addFormatter('class','hindi');
		$grid->addColumn('Expander','allot_item');

		if($form->isSubmitted()){
			$this->api->memorize('issue_month',$form->get('for_month'));
			$grid->js()->reload(array('store_no'=>$form->get('store_no')))->execute();
		}
	}

	function page_allot_item(){
		$this->api->stickyGET('student_id');
		// $this->add('Text')->set($_GET['student_id']);
		// $this->add('Text')->set($this->api->recall('date'));
		try{
			// $t=$this->add('Model_Item');

			$ism=$this->add('Model_Item_Issue');
			$ism->addCondition('student_id',$_GET['student_id']);
			$ism->addCondition('month',$this->api->recall('issue_month'));
			$ism->getElement('rate')->setvalueList($this->api->recall('rates_selected',array('-')));
			// $t->addCondition('is_stationory',1);

			// $ism->debug();
			$crud=$this->add('CRUD');
			if($crud->grid){
				$crud->grid->addColumn('sno','Sno');	
			}
			$crud->setModel($ism,null,array('sno','item','quantity','date','rate','amount','is_stationory'));
			if($crud->form){
				// Generate last date of selected month as per selected session
				$cur_sesssion=$this->add('Model_Sessions_Current')->tryLoadAny();
				$month=$this->api->recall('issue_month');
				$year = ($month > (int)date('m',strtotime($cur_sesssion['end_date'])))? date('Y',strtotime($cur_sesssion['start_date'])) : date('Y',strtotime($cur_sesssion['end_date']));
				// echo $month . " :: " . (int)date('m',strtotime($cur_sesssion['end_date']));
				$crud->form->getElement('date')->set(date("$year-$month-28	")); //TODO year must be between session
				
				$crud->form->getElement('item_id')->setAttr('class','hindi');
				$item_field=$crud->form->getElement('item_id');
				$crud->form->getElement('item_id')->model->addCondition('category_id',1);
				$rate_field= $crud->form->getElement('rate');//->destroy();
				// $rate_field = $crud->form->addField('dropdown','rate');

				if($_GET['changed_item']){
					$itm=$this->add('Model_Item_Inward');
					$q=$itm->dsql()->del('field')
						->field('DISTINCT(rate) collected_rate')
						->field('inward_date')
						->join('bill_master','bill_id')
						->where('item_id',$_GET['changed_item'])->order('inward_date','desc');
					$default_rate=false;
					$r_array=array();
					foreach($q as $junk){
						if(!$default_rate) $default_rate = $junk['collected_rate'];
						$r_array += array($junk['collected_rate']=>$junk['collected_rate']);
					}
					$this->api->memorize('rates_selected',$r_array);
					$rate_field->setValueList($r_array)->validateNotNull("rate is must")->set($default_rate);
					// $rate_field->set(implode(",",$r_array));
				} 

				if($crud->form->isSubmitted()){
					if(strpos($crud->form->get('rate'),","))
						$crud->form->displayError('Please enter only one rate','rate');
				}

				$item_field->js('change',$crud->form->js()->atk4_form('reloadField','rate',array($this->api->url(),'changed_item'=>$item_field->js()->val())));

			}
			if($crud->grid){
				$crud->grid->setFormatter('item','hindi');
				$crud->grid->setFormatter('amount','number');
				$crud->grid->addTotals(array('amount'));
				
			}
		}catch(Exception $e){
			$this->js()->univ()->errorMessage($e->getMessage())->execute();

		}
	}
}