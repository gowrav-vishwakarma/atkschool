<?php


class page_store_MultiRecieptPrint extends Page {
	function init(){
		parent::init();
	 	$acl=$this->add('xavoc_acl/Acl');
      	$this->api->stickyGET('store_no');
      	$month = $this->api->stickyGET('month');
      	$store_nos=$_GET['store_no'];
      	$stores=explode('-', $store_nos);
      	$start_store = $stores[0];
      	$end_store = $stores[1];


      	for($i=$start_store; $i<= $end_store; $i++){
			$this->add('H3')->setHtml("Bal Vinay Mandir Senior Secondary School,Udaipur<br/><small>Session: ".$this->add('Model_Sessions_Current')->tryLoadAny()->get('name')."</small>")->setAttr('align','center');
			    $v=$this->add('View_ReceiptAll',array('store_no'=>$i,'month'=>$_GET['month']),null,array('view/receiptAllPrint'));
	    	if($_GET['month']){
	    		$v=$this->add('View_Receipt',array('store_no'=>$i,'month'=>$_GET['month']),null,array('view/receipt'));
	    	}else{
			    $v=$this->add('View_ReceiptAll',array('store_no'=>$i,'month'=>$_GET['month']),null,array('view/receiptAllPrint'));
	    	}
	    	// $v->grid->template->trySet('table_width','75%');
	  	}
	}

	function render(){
		$this->api->template->del('header');
		$this->api->template->del('Footer');
		parent::render();
	}
}