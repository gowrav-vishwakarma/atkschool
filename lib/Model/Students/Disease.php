<?php
class Model_Students_Disease extends Model_Table{
		var $table="disease_master";
	function init(){
		parent::init();
		$this->hasOne('Hosteler','student_id');
		$this->hasOne('Diseases','disease_id');
		// $this->addField('disease')->display('hindi');
		$this->addField('report_date')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('treatment')->type('boolean')->defaultValue(false)->caption('Treatment Completed');
		$this->addField('treatment_date')->type('date')->defaultValue(date('Y-m-d'));

		// $this->addExpression("name")->set(function($m,$q){
		// 	return $m->refSQL('student_id')->fieldQuery('name');
		// });
		// // $this->_dsql()->order('student_id','asc');
		
	}
}