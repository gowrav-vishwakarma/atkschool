<?php
class page_correction3 extends Page{
	function init(){
		parent::init();
 		$this->query("ALTER TABLE `news` CHANGE `name` `name` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ");
		$this->query("ALTER TABLE `staff_master` ADD `pan_no` INT NOT NULL ,
		ADD `remarks` TEXT NOT NULL ");
		$this->query("ALTER TABLE `item_master` ADD `is_stationory` TINYINT NOT NULL DEFAULT '0'");
		$this->query("DELETE from item_inward  WHERE id=20");
		$this->query("DELETE from item_inward  WHERE id=21");	
	}
	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }
}