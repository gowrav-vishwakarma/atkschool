<?php
class page_correction4 extends Page{
	function init(){
		parent::init();
		$this->query("DELETE FROM `school`.`student_attendance` WHERE `student_attendance`.`id` = 397");	
	}
	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }
}