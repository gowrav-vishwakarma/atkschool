<?php

class Model_Item_Category extends Model_Table{
	var $table="item_category";
	function init(){
		parent::init();

		$this->hasMany('Item','category_id');
		$this->addField('name')->caption('Category')->mandatory("Please provide a name")->display('hindi');
		
		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if($this->ref('Item')->count()->getOne())
			throw $this->exception('Category has Items Associated, Cannot Delete this category');
	}
}