<?php
class View_ReceiptAll extends View {
	public $store_no;
	public $month;
	public $grid;

	function init(){
		parent::init();


		$st=$this->add('Model_Hosteler');
		$st->addCondition('store_no',$this->store_no);
		$st->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$st->setOrder('store_no','asc'); 
		$st->tryLoadAny();
		if(!$st->loaded()) {
			$this->destroy();
			return;
		}
		$sc=$st->ref('scholar_id');
		// $sc->addField('father_name');
		
		$ism = $st->ref('Item_Issue');
		$ism->addExpression('date_month')->set('FORMAT(Month(date),"M")');
		if($this->month){
			$ism->addCondition('date_month',$this->month);
		}
		$ism->addExpression('total_qty')->set('sum(quantity)');
		$ism->addExpression('total_amount')->set('round(sum(quantity * rate))');
		$ism->_dsql()->group('date_month');
		$ism->_dsql()->order('receipt_no','asc');

		// $ism_2=clone $ism;
		// $receipt_no=$ism_2->_dsql()->limit(1)->del('field')->field('receipt_no')->getRow();
		// // print_r($receipt_no);
		// $receipt_no= $receipt_no[1];

		$this->template->trySet('student_name',$st['name']);
		$this->template->trySet('father_name',$sc['father_name']);
		$this->template->trySet('class_name',$st->ref('class_id')->get('name'));
		$this->template->tryDel('receipt');
		$this->template->trySet('store_no',$st['store_no']);
		// $this->template->trySet('month',date("M",strtotime("2000-".$_GET['month']."-01")));

		$this->grid=$this->add('Grid',null,null,array('condensegrid'));
		$this->grid->addColumn('sno','sno');

		// $ism->debug();
		$this->grid->setModel($ism,array('sno','date_month','receipt_no','total_amount'));
		$this->grid->addFormatter('date_month','month');
		$this->grid->setFormatter('total_amount','money');
		// $this->grid->setFormatter('total_qty','number');

		$this->grid->addTotals(array('total_amount'));
		// if($this->month == 0){
		// 	foreach ($ism as $is) {
		// 		$v=$this->add('View_Receipt',array('store_no'=>$this->store_no,'month'=>$ism['date_month']),null,array('view/receipt'));
		// 	}
		// }
		$this->api->welcome->destroy();


	}

	function defaultTemplate(){
		return array('view/receiptAll');
	}

	function render(){
		$this->api->template->tryDel('logo');
		$this->api->template->tryDel('Menu');

		parent::render();
	}
}