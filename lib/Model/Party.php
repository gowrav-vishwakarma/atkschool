<?php

class Model_Party extends Model_Table{
	var $table="party_master";
	function init(){
		parent::init();

		$this->addField('ename')->mandatory("Name is Must");
		$this->addField('name')->display('hindi')->mandatory("Name is Must");
		$this->addField('contact')->mandatory("Conatct Number Must");
		$this->addField('address')->display('hindi')->mandatory("Address is Must");
		$this->addField('is_mesh_supplier')->type('boolean')->defaultValue(false);

		$this->hasMany('Bill','party_id');
		$this->hasMany('Mesh_ItemConsume','party_id');
		$this->hasMany('Mesh_ItemInward','party_id');
		$this->addHook('beforeDelete',$this);

		$this->_dsql()->order('ename','asc');
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeDelete(){
		if($this->ref('Bill')->count()->getOne())
			throw $this->exception("You Can not Delete Party, It Conatains Bill(s)");
	}
}