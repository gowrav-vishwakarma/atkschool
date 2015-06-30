<?php
class Model_MainBlockExam extends Model_Table{
	var $table = "main_block_exam";

	function init(){
		parent::init();
		$this->hasOne("MainBlock","main_block_id")->caption("Block Name");
		$this->hasOne("ExamClassMap","exammap_id")->caption("Exam");
		$this->addField('max_marks')->display(array('grid'=>'grid/inline'));
	}
}