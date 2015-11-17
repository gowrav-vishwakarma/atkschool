<?php
class Model_Fee extends Model_Table{
	var $table="fee";
	function init(){
	
		parent::init();

		$this->hasOne('FeesHead','feehead_id');
		$this->hasOne('Sessions_Current','session_id');
		$this->addField('name');
		$this->addField('isOptional')->type('boolean');
		$this->addField('for_hostler_only')->type('boolean');
		$this->addField('scholaredamount');

		// $this->hasMany('Fees_Applicable','fee_id');
		// $this->hasMany('Fees_Deposite','fee_id');
		$this->hasMany('FeeClassMapping','fee_id');
		$this->add('dynamic_model/Controller_AutoCreator');
	}
}