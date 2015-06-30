<?php

class Model_Bill extends Model_Table{
	var $table="bill_master";

	function init(){
		parent::init();

		$this->hasOne('Party','party_id');
		$this->addField('name')->caption('Bill No');
		$this->addField('bill_date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('inward_date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('paid')->type('boolean')->defaultValue(false);
		$this->addField('cheque_date')->type('date')->defaultValue(null);
		$this->addField('cheque_number');
		$this->hasOne('Sessions_Current','session_id');

		$this->hasMany('Item_Inward','bill_id');

		$this->addExpression('no_of_items')->set(function($m,$q){
			return $m->refSQL('Item_Inward')->count();
		});

		$this->addExpression('bill_amount')->set(function($m,$q){
			return $m->refSQL('Item_Inward')->sum($q->expr('quantity * rate'));
		});

		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->fieldQuery('id'));

		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if($this['no_of_items']>0){
			throw $this->exception("Bill contains items and cannot be deleted, remove items first");
		}
	}
}