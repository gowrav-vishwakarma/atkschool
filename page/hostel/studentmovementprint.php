<?php
class page_hostel_studentmovementprint extends Page{
	function init(){
		parent::init();

		$this->add('View_PrintStudentMove');
	}
}