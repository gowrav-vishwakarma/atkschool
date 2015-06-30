<?php
class Model_Mesh_Item extends Model_Item{
	function init(){
		parent::init();

		$this->addCondition('category_id',2);
		$this->hasMany('Mesh_ItemInward','item_id');
		$this->hasMany('Mesh_ItemConsume','item_id');
		
	}
}