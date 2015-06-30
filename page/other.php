<?php
class page_other extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');
		$t=$this->add('Model_Item');
		// $grid->getElement('category')->setFormatter('class','hindi');
		$t->addCondition('is_stationory',false);
		$grid->setModel($t,array('name','LastPurchasePrice','TotalInward','TotalIssued','instock'));
	}
}