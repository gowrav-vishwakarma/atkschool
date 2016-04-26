<?php

class page_printfeesreceipt extends Page{
	function init(){
		parent::init();
		$feehead=$_GET['FeeHead'];
		
		if($feehead=='Tuation Fee'){
			$this->add('View_FeeHead_TuationFeeReceipt');
		}elseif ($feehead=='School Fee') {
			$this->add('View_FeeHead_SchoolFeeReceipt');
		}else{
			$this->add('View_FeeHead_HostelFeeReceipt');
		}
	}
}