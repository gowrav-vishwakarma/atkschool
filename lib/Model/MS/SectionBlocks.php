<?php
class Model_MS_SectionBlocks extends Model_Table{
	var $table = "marksheet_section_blocks";

	function init(){
		parent::init();

		$this->addField("name")->caption("Marksheet Name");
		$this->addField("is_total_required")->type('boolean');
		$this->addField("total_title")->caption('Total Title');
		$this->addField("column_code")->caption('Column Code');
		
		$this->hasOne("MS_Sections","marksheet_sections_id");
		$this->hasMany("MS_BlockExams","marksheet_section_blocks_id");

		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if($this->ref('MS_BlockExams')->count()->getOne()>0)
			throw $this->exception("You can Not Delete It, It Contains Block Exams");
	}
}