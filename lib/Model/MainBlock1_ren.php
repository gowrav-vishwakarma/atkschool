<?php
class Model_MainBlock extends Model_Table{
	var $table = "main_block";
	function init(){
		parent::init();

		$this->hasOne("MarksheetDesigner","marksheet_designer_id");
		$this->addField("name")->caption("Block Name");
		$this->addField("is_total_required")->type('boolean');
		$this->addField("total_title")->caption('Total Title');

		$this->hasMany("MainBlockExam","main_block_id");
	}
}