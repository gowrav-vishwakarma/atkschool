<?php

class Model_FeesHead extends Model_TableSafe{
	var $table="fee_heads";
	function init(){
		parent::init();

		$this->addField('name');
		$this->hasMany('Fee','feehead_id');
		// $this->addHook('beforeDelete',$this);
	}

    function beforeDelete(){
		if($this->ref('Fee')->count()->getOne()>0)
		throw $this->exception("Fees Head Can't Delete, It Conatains Fees");    	
    }
}