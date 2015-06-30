<?php
class Model_MS_BlockExams extends Model_Table{
	var $table = "marksheet_blocks_exam";

	function init(){
		parent::init();
		$this->hasOne("MS_SectionBlocks","marksheet_section_blocks_id")->caption("Block Name");
		$this->hasOne("ExamClassMap","exammap_id")->caption("Exam");
		$this->addField('column_code')->display(array('grid'=>'grid/inline'));

	}


}