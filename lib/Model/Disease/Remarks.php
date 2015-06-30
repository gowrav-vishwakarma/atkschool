<?php
class Model_Disease_Remarks extends Model_Table{
	var $table="disease_remarks";
	function init(){
		parent::init();

		$this->hasOne('Diseases','disease_id');
		$this->addField('remarks')->type('text')->mandatory('It is Must');
		$this->addField('created_at')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));

	}
}