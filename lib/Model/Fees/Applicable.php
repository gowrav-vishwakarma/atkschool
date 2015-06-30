<?php
class Model_Fees_Applicable extends Model_Table{
	var $table="fee_applicable";
	function init(){
		parent::init();

		$this->hasOne('Student','student_id');
		$this->hasOne('FeeClassMapping','fee_class_mapping_id')->caption('Fee Applicable');
		// $this->hasOne('Fee','fee_id');
		$this->addField('amount');
		
		// $this->addField('due')->defaultValue(0);


		$this->hasMany('Fees_Deposit','fee_applicable_id');

		$this->addExpression('paid')->set(function ($m,$q){
			// return $m->dsql()->expr('IFNULL((select sum(paid) from `fee_deposit_master` where `fee_deposit_master`.`fee_applicable_id` = `fee_applicable`.`id` ),0)');
			return $m->refSQL('Fees_Deposit')->dsql()->del('field')->field('sum(paid)');
		});

		$this->addExpression('due')->set(function ($m,$q){
			return $m->dsql()->expr($m->table.'.amount - IFNULL((select sum(paid) from `fee_deposit_master` where `fee_deposit_master`.`fee_applicable_id` = `fee_applicable`.`id` ),0)');
			// return $m->dsql()->expr('amount - paid');
		});

		$this->addExpression('name_xyz')->set(function($m,$q){
			return $m->api->db->dsql()->table('fee')->field('name')->where('feehead_id',$m->refSQL('fee_class_mapping_id')->fieldQuery('feehead_id'))->limit(1);
		});
		
		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){
		if($this->loaded()){
			if($this['amount'] < $this['paid']) throw $this->exception("Amount can not be less then paid amount");
		}
	}

	function beforeDelete(){
		if($this->ref("Fees_Deposit")->count()->getOne() > 0)
			 throw $this->exception("you can not delete it, It contains deposits");
	}

	function submitFee($amount,$onDate,$receiptNo){
		
	}

	function promote($from_session, $to_session){

	}
}