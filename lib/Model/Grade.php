<?php
class Model_Grade extends Model_Table{
	var $table = "grade_master";
	
	function init(){
		parent::init();

		$this->addField("name")->caption("Grade");
		$this->addField("percent_above");
		$this->hasOne("Session","session_id");
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
	}

	function calculate($marks_obtain, $total_marks ){
		$grade=$this->add('Model_Grade')->setOrder('percent_above');
		$percentage = $marks_obtain / $total_marks * 100.00;

		foreach($grade as $g_junk){
			if($percentage >= $grade['percent_above']) return $grade['name'];
		}

		return 'Fail';

	}
}