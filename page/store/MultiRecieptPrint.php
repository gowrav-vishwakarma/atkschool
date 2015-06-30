<?php


class page_store_MultiRecieptPrint extends Page {
	function init(){
		parent::init();
		$this->add('H3')->setHtml("Bal Vinay Mandir Senior Secondary School,Udaipur<br/><small>Session: ".$this->add('Model_Sessions_Current')->tryLoadAny()->get('name')."</small>")->setAttr('align','center');
	 	$acl=$this->add('xavoc_acl/Acl');
      	$this->api->stickyGET('store_no');
      	$store_nos=$_GET['store_no'];
      	$stores=explode('-', $store_nos);
      	$start_store = $stores[0];
      	$end_store = $stores[1];


      	for($i=$start_store; $i<= $end_store; $i++){
	    	$v=$this->add('View_ReceiptAll',array('store_no'=>$i),null,array('view/receiptAllPrint'));
	    	// $v->grid->template->trySet('table_width','75%');
	  	}


	}

	function render(){
		$this->api->template->del('header');
		$this->api->template->del('Footer');
		parent::render();
	}
}