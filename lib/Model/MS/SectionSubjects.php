<?php
class Model_MS_SectionSubjects extends Model_Table{
	var $table = "marksheet_section_subjects";

	function init(){
		parent::init();
		$this->hasOne('MS_Sections','marksheet_section_id');
		$this->hasOne('Subject','subject_id');
		$this->hasOne('Session','session_id');
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));		
	}
}