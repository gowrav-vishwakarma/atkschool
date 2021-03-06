<?php

class Model_Mesh_ItemConsume extends Model_Table{
	var $table="mesh_item_consume";

	function init(){
		parent::init();
		

		$this->hasOne('Mesh_Item','item_id');
		$this->hasOne('Party','party_id');
		$this->hasOne('Session','session_id');

		$this->addField('quantity')->mandatory('quantity is Must To Select');
		$this->addField('unit')->enum(array('Packet','Kg','Liter'))->mandatory('quantity is Must To Select');
		$this->addField('date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('remarks')->type('text')->mandatory('Remarks is Required');
		// $this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));

		$this->add('dynamic_model/Controller_AutoCreator');
		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}
	function beforeSave(){
		$itemInward=$this->add('Model_Mesh_ItemInward');
		$itemInward->addCondition('item_id',$this['item_id']);
		$itemInward->tryLoadAny();
		if($this['unit']!=$itemInward['unit']){
			throw new Exception("wrong Unit", 1);
		}
		if($itemInward->loaded()){
				// throw new Exception($itemInward['item_id'], 1);
			if($itemInward['quantity'] < $this['quantity'])
				throw new Exception("There is no sufficient Item for consume1");
				// $this->api->js()->univ()->errorMessage('There is no sufficient Item');
		}else{

			if($itemInward['quantity'] < $this['quantity'])
				throw new Exception("There is no sufficient Item for consume2");
		}
			
		$new_stock = $this['quantity'];
		$item_m=$this->ref('item_id');
		$item_m['stock'] = $item_m['stock'] - $new_stock;
		$item_m->save();
	}

	function beforeDelete(){
		$old_stock = $this['quantity'];
		$item_m=$this->ref('item_id');
		$item_m['stock'] = $item_m['stock'] + $old_stock;
		$item_m->save();	
	}
}