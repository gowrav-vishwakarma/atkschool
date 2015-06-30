<?php

class page_correct extends Page {
	function init(){
		parent::init();

		// $this->query('ALTER TABLE `marksheet_designer` ADD `declare_date` DATETIME NOT NULL ');
		// $this->query('DROP TABLE IF EXISTS `grade_master` ;
		// 				CREATE TABLE `grade_master` (
		// 				`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
		// 				`percent_above` int( 11 ) NOT NULL ,
		// 				`name` varchar( 1 ) NOT NULL ,
		// 				`session_id` int( 11 ) NOT NULL ,
		// 				PRIMARY KEY ( `id` )
		// 				) ENGINE = InnoDB DEFAULT CHARSET = latin1;');
		// $this->query(" ALTER TABLE `fee` ADD `for_hostler_only` TINYINT NOT NULL AFTER `scholaredamount` ");
		

		// $this->query('ALTER TABLE `student` ADD `result_stopped` TINYINT NOT NULL ');
		$this->query("  ALTER TABLE `hosteller_outward` ADD COLUMN `session_id`  int(11) NOT NULL AFTER `remark`");
		$this->query("  ALTER TABLE `rooms` ADD `session_id` INT NOT NULL ");
		// $this->query("  ALTER TABLE `fee_deposit_master` CHANGE `remarks` `remarks` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ");
		$this->query("  ALTER TABLE `staff_outward` ADD COLUMN `session_id`  int(11) NOT NULL AFTER `staff_id`");
		// $this->query("  ALTER TABLE `student` MODIFY COLUMN `result_stopped`  tinyint(4) NOT NULL AFTER `is_present`");
		$this->query("  ALTER TABLE `staff_master` ADD `is_active` TINYINT NOT NULL");
		$this->query(" ALTER TABLE `scholars_master` ADD `previouse_school_name` VARCHAR( 255 ) NOT NULL");
		$this->query(" ALTER TABLE `scholars_master` ADD `previouse_class_name` VARCHAR( 255 ) NOT NULL");
		$this->query(" ALTER TABLE `scholar_guardian` ADD `is_active` TINYINT NOT NULL DEFAULT '0'");


		// SECOND FILE CORRECTIONS ....
		// $this->query('ALTER TABLE `hosteller_outward` ADD `session_id` INT NOT NULL ');
		$this->query('UPDATE `rooms` SET `session_id`=8');
		$this->query('UPDATE `hosteller_outward` SET `session_id`=8');
		$this->query('UPDATE `staff_outward` SET `session_id`=8');





	}

	function query($q){
		$this->api->db->dsql($this->api->db->dsql()->expr($q))->execute();
	}
}