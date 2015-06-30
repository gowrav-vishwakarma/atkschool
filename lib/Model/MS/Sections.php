<?php
class Model_MS_Sections extends Model_Table{
	var $table = "marksheet_sections";

	function init(){
		parent::init();

		$this->addField("name")->caption("Section Name");
		$this->addField("has_grand_total")->type('boolean')->caption("Has Grand Total");
		$this->addField("extra_totals")->caption("Extra Totals")->display(array('grid'=>'shorttext'));
		$this->addField("max_marks_for_each_subject")->type('boolean')->caption("MM 4 Every Row");
		$this->addField("grade_decider")->type('boolean');
		$this->addField("show_grade")->type('boolean');
		$this->addField("total_at_bottom")->type('boolean');
		
		$this->hasOne("MS_Designer","marksheet_designer_id");
		$this->hasMany("MS_SectionBlocks","marksheet_sections_id");
		$this->hasMany("MS_SectionSubjects","marksheet_section_id");

		$this->addHook('beforeDelete',$this);
	}

	function beforeDelete(){
		if($this->ref('MS_SectionBlocks')->count()->getOne()>0)
			throw $this->exception("You can not Delete this Sections,It contains Blocks and Associated Subjects");
	}
}