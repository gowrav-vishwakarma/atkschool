<?php
class Model_Division extends Model_Table{
	var $table = "division_master";
	function init(){
		parent::init();
		
		$this->addField("name")->caption("Division");
		$this->addField("percentage");
		$this->hasOne("Session","session_id");
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
	}
}