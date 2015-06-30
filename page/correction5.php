<?php
class page_correction5 extends Page{
	function init(){
		parent::init();
		$this->query("ALTER TABLE `session_master` ADD `start_date` DATE NOT NULL ");	
		$this->query("ALTER TABLE `session_master` ADD `end_date` DATE NOT NULL ");	
		$this->query("ALTER TABLE `item_inward` CHANGE `quantity` `quantity` FLOAT( 11 ) NULL DEFAULT NULL ");	
		$this->query("ALTER TABLE `item_issue` CHANGE `quantity` `quantity` FLOAT( 11 ) NOT NULL ");	
		// spaces in student_marks and fee applicable date field
		// fee_applicable and deposit due=aamount where due is null
	}
	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }
}