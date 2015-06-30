<?php
class page_correction7 extends Page{
	function init(){
		parent::init();
		$q="
			/*ALTER TABLE `disease_remarks` ADD COLUMN `disease_id`  int(11) NOT NULL AFTER `created_at`;*/
			ALTER TABLE `marksheet_sections` ADD COLUMN `show_grade`  tinyint(4) NOT NULL DEFAULT 1 AFTER `total_at_bottom`;
		";

		$this->query($q);	
		$this->query("ALTER TABLE `marksheet_sections` ADD `extra_totals` VARCHAR( 255 ) NOT NULL");	
		$this->query("ALTER TABLE `marksheet_sections` ADD `column_code` VARCHAR( 255 ) NOT NULL ");	
		
		}
	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }
}