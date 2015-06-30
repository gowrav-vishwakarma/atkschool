<?php
class page_stationory extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');
		$t=$this->add('Model_Item');
		$t->addCondition('is_stationory',true);
		$grid->setModel($t,array('name','LastPurchasePrice','TotalInward','TotalIssued','instock'));

	}
}