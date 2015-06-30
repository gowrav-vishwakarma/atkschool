<?php
class View_TotalReceipt extends View {
	public $store_no;
	public $month;
	public $grid;

	function init(){
		parent::init();
		$st=$this->add('Model_Hosteler');
		$st->addCondition('store_no',$this->store_no);
		// $st->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		 
		$st->tryLoadAny();
		
		$sc=$st->ref('scholar_id');
		
		//========== Get Item Issue Table

		$ism = $st->ref('Item_Issue');
		$ism->addExpression('date_month')->set('FORMAT(Month(date),"M")');

		$ism->addExpression('total_store_amount')->set('sum(quantity * rate)');
		$ism->_dsql()->group('date_month');
		$ism->_dsql()->order('receipt_no','asc');

		$final_array=array();

		foreach($ism as $junk){
			$final_array[$ism['date_month']]['total_store_amount'] = $ism['total_store_amount'];
			$final_array[$ism['date_month']]['date_month'] = $ism['date_month'];
			$final_array[$ism['date_month']]['total'] += $ism['total_store_amount'];

		}

		//============ Fee Submitted

		$fee_deposits = $this->add('Model_Fees_Deposit');
		$fee_applicable = $fee_deposits->join('fee_applicable.id','fee_applicable_id');
		$fee_class_mapping = $fee_applicable->join('fee_class_mapping.id','fee_class_mapping_id');
		$fees = $fee_class_mapping->join('fee.id','fee_id');
		$fees->addField('name');
		$student = $fee_applicable->join('student.id','student_id');
		$student->addField('store_no');
		$fee_deposits->addCondition('store_no',$this->store_no);

		$fee_deposits->addExpression('date_month')->set('FORMAT(Month(deposit_date),"M")');
		$fee_deposits->addExpression('total_fee_amount')->set('sum(paid)');
		$fee_deposits->_dsql()->group('date_month');
		$fee_deposits->_dsql()->group('name');
		// $fee_deposits->_dsql()->order('receipt_no','asc');

		foreach($fee_deposits as $junk){
			$final_array[$fee_deposits['date_month']][$fee_deposits['name']] = $fee_deposits['total_fee_amount'];
			$final_array[$fee_deposits['date_month']]['date_month'] = $fee_deposits['date_month'];
			$final_array[$fee_deposits['date_month']]['total'] += $fee_deposits['total_fee_amount'];
		}


		// $fee_deposits->rewind();
		// $gt=$this->add("Grid");
		// $gt->setModel($fee_deposits,array('date_month','total_fee_amount','name'));


		$this->template->trySet('student_name',$st['name']);
		$this->template->trySet('father_name',$sc['father_name']);
		$this->template->trySet('class_name',$st->ref('class_id')->get('name'));
		$this->template->tryDel('receipt');
		$this->template->trySet('store_no',$st['store_no']);
		// $this->template->trySet('month',date("M",strtotime("2000-".$_GET['month']."-01")));

		$this->grid=$this->add('Grid');
		$this->grid->addColumn('sno','sno');

		// $ism->debug();
		$this->grid->addColumn('date_month');
		$this->grid->setFormatter('date_month','month');
		foreach($fee_deposits as $junk){
			$this->grid->addColumn($fee_deposits['name']);
		}

		$this->grid->addColumn('total_store_amount');
		$this->grid->addColumn('total');
		$this->grid->setSource($final_array);
		$this->grid->setFormatter('total','number');
		$this->grid->addTotals();

		$this->api->welcome->destroy();


	}

	function defaultTemplate(){
		return array('view/receiptAll');
	}

	function render(){
		$this->api->template->del('logo');
		$this->api->template->del('Menu');

		parent::render();
	}
}