<?php
class View_Receipt extends View {
	public $store_no;
	public $month;
	public $grid;

	function init(){
		parent::init();

		
		$st=$this->add('Model_Hosteler');
		$st->addCondition('store_no',$this->store_no);
		$st->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		
		$st->tryLoadAny();
		$sc=$st->ref('scholar_id');
		
		$ism = $st->ref('Item_Issue');
		$ism->addExpression('date_month')->set('Month(date)');
		if($this->month){
			$ism->addCondition('date_month',$this->month);
		}
		// throw new Exception("hjjhjh".$_GET['month'].$ism['date_month']);
		$ism->addExpression('total_qty')->set('sum(quantity)');
		$ism->addExpression('total_amount')->set('sum(quantity * rate)');
		$ism->_dsql()->group('item_id')->group('rate')->group('date_month');

		$ism_2=clone $ism;
		$receipt_no=$ism_2->_dsql()->limit(1)->del('field')->field('receipt_no')->getRow();
		// print_r($receipt_no);
		$receipt_no= $receipt_no[1];

		$this->template->trySet('student_name',$st['name']);
		$this->template->trySet('father_name',$sc['father_name']);
		$this->template->trySet('class_name',$st->ref('class_id')->get('name'));
		$this->template->trySet('receipt',$receipt_no);
		$this->template->trySet('store_no',$st['store_no']);
		$this->template->trySet('month',date("M",strtotime("2000-".$this->month."-01")));

		$this->grid=$this->add('Grid',null,null,array('condensegrid'));
		$this->grid->addColumn('sno','sno');

		// $ism->debug();
		$this->grid->setModel($ism,array('sno','item','total_qty','month','rate','total_amount'));
		$this->grid->addFormatter('item','hindi');
		// $this->grid->addFormatter('sno','sno');
		$this->grid->setFormatter('total_amount','money');
		$this->grid->setFormatter('total_qty','number');

		$this->grid->addTotals(array('total_amount'));

		$this->api->welcome->destroy();


	}

	function defaultTemplate(){
		return array('view/receipt');
	}

	function render(){
		$this->api->template->del('logo');
		$this->api->template->del('Menu');

		parent::render();
	}
}