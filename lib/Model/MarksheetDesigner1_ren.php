<?php
class Model_MarksheetDesigner extends Model_Table{
	var $table = "marksheet_designer";

	function init(){
		parent::init();

		$this->addField("name")->caption("Marksheet Name");
		$this->addField("declare_date")->caption("Result Declare Date");
		$this->hasOne("Class","class_id");
		$this->hasOne("Session","session_id");
		$this->hasMany("MainBlock","marksheet_designer_id");
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
	}
}