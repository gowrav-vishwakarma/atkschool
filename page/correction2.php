<?php
class page_correction2 extends Page{
	function init(){
		parent::init();
 		$this->query("UPDATE `hosteller_outward` SET `student_id` =456 WHERE `student_id` =371");
		$this->query("DELETE from scholars_master  WHERE id=175");
		$this->query("DELETE from student  WHERE scholar_id=175");
		$this->query("UPDATE `hosteller_outward` SET student_id=406 WHERE student_id=177");
		// $this->query("ALTER TABLE `fee_applicable` CHANGE `fee_id` `fee_class_mapping_id` INT( 11 ) NOT NULL");
		// $this->query("ALTER TABLE `fee_deposit_master` CHANGE `fee_id` `fee_applicable_id` INT( 11 ) NOT NULL ");
		$this->query("
						CREATE TABLE `student_marks` (
						`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
						`student_id` int( 11 ) NOT NULL ,
						`examsub_map_id` int( 11 ) NOT NULL ,
						`marks` int( 11 ) NOT NULL ,
						PRIMARY KEY ( `id` )
					) ENGINE = InnoDB DEFAULT CHARSET = latin1;

				");
	$this->query("
					CREATE TABLE `student_attendance` (
				`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
				`class_id` int( 11 ) NOT NULL ,
				`student_id` int( 11 ) NOT NULL ,
				`session_id` int( 11 ) NOT NULL ,
				`month` int( 11 ) NOT NULL ,
				`total_attendance` int( 11 ) NOT NULL ,
				`present` int( 11 ) NOT NULL ,
				PRIMARY KEY ( `id` )
				) ENGINE = InnoDB DEFAULT CHARSET = latin1;
		");
	$this->query("CREATE TABLE `disease_remarks` (
				`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
				`remarks` text NOT NULL ,
				`created_at` datetime NOT NULL ,
				PRIMARY KEY ( `id` )
				) ENGINE = InnoDB DEFAULT CHARSET = latin1;
			");

	$this->query("TRUNCATE fee_applicable");
	$this->query("TRUNCATE fee_class_mapping");
	$this->query("TRUNCATE fee_deposit_master");

	}
	
	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }
	
}