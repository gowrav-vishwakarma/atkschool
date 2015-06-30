<?php

class Model_Fees_Deposit extends Model_Table{
	var $table="fee_deposit_master";
	function init(){
		parent::init();

		$this->hasOne('Fees_Applicable','fee_applicable_id');
		// $this->addField('due');
		$this->addField('paid');
		// $this->addField('due_date')->type('date');
		$this->addField('deposit_date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('receipt_number');
		$this->addField('remarks');

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}


	function beforeSave(){
		
	}

	function beforeDelete(){

	}
}