<?php
class Model_MS_Designer extends Model_Table {
	var $table = "marksheet_designer";

	function init(){
		parent::init();

		$this->addField("name")->caption("Marksheet Name");
		$this->addField("declare_date")->caption("Result Declare Date")->type('date')->defaultvalue(date('Y-m-d'));
		$this->addField("show_grade")->type('boolean')->caption("Show Grade")->defaultvalue(false);
		$this->hasOne("Class","class_id");
		$this->hasOne("Session","session_id");
		$this->hasMany("MS_Sections","marksheet_designer_id");

		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));

		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if($this->ref('MS_Sections')->count()->getOne()>0) throw $this->exception("You can Not Delete, It Contains Sections");
	}
}