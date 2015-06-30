<?php

class Model_Item_Inward extends Model_Table{
	var $table="item_inward";

	function init(){
		parent::init();

		$this->hasOne('Item','item_id');
		$this->hasOne('Bill','bill_id');
		// $this->hasOne('Session','session_id');
		$this->addField('quantity');
		$this->addfield('rate');
		// $this->addfield('date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addExpression('Amount')->set('quantity * rate');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if(!$this->loaded()){
			$old_value=0;
		}else{
			$old=$this->add('Model_Item_Inward');
			$old->load($this->id);
			$old_value = $old['quantity'] - $this['quantity'];
		}
		$new_stock = $this['quantity'] + $old_value;

		$item_m=$this->ref('item_id');
		$item_m['stock'] = $item_m['stock'] + $new_stock;
		$item_m->save();
	}
}