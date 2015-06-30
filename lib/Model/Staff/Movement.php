<?php

class Model_Staff_Movement extends Model_Table {
	var $table= "staff_outward";
	function init(){
		parent::init();
		$this->hasOne('Staff_All','staff_id');
		$this->hasOne('Sessions_Current','session_id');
		$this->addField('date')->type('date')->defaultValue(date('Y-m-d H:i:s'))->display(array('grid'=>'datetime'));
		$this->addField('action')->enum(array('inward','outward'))->display(array('grid'=>'attendance'));
		
		$this->addExpression('name')->set(function($m,$q){
     			return $m->refSQL('staff_id')->fieldQuery('ename');
     		});

		$this->addHook('beforeSave',$this);

	}

	function beforeSave(){
		if(!$this->loaded()) $this['session_id'] = $this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
	}

}