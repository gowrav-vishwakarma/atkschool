<?php

class Model_Consume extends Model_Table{
	var $table="genral_item_consume";

	function init(){
		parent::init();
		

		$this->hasOne('Session','session_id');
		$this->hasOne('Item','item_id');

		$this->addField('quantity')->mandatory('quantity is Must To Select');
		$this->addField('remarks')->type('text')->mandatory('quantity is Must To Select');
		$this->addField('date')->type('date')->defaultValue(date('Y-m-d'));
		$this->add('dynamic_model/Controller_AutoCreator');
		$this->addHook('beforeSave',$this);
	}
	function beforeSave(){
		$itemInward=$this->add('Model_Item_Inward');
		$itemInward->addCondition('item_id',$this['item_id']);
		$itemInward->tryLoadAny();
		if($itemInward->loaded()){
			if($itemInward['quantity'] < $this['quantity'])
				throw new Exception("There is no sufficient Item for consume");
				// $this->api->js()->univ()->errorMessage('There is no sufficient Item');
		}
	}
}