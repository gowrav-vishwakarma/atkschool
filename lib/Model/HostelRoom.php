<?php

class Model_HostelRoom extends Model_Table{
	var $table="rooms";

	function init(){

		parent::init();

		$this->hasOne('Hostel','hostel_id');
		$this->hasOne('Sessions_Current','session_id');
		$this->addField('room_no');
		$this->addField('capacity');
	
		// $this->hasMany('HostelRoom','room_id');
		$this->hasMany('RoomAllotement','room_id');

		$this->addExpression('vacant')->set('id')->display('diff');
		$this->addExpression('alloted')->set(function ($m,$q){
			return $m->refSQL('RoomAllotement')->count();
		});

		$this->addExpression('name')->set('room_no');

		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$this->_dsql()->order('room_no','asc');
		$this->addHook('beforeSave',$this);
		
	}
	function beforeSave(){
		if($this['capacity']< $this['alloted']) throw $this->exception("Capcity can  not be less then Alloted");
	}

	
}