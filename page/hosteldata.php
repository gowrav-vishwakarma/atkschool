<?php

class page_hosteldata extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$tab=$this->add('Tabs');
		
		$tab->addTabURL('hostel_hostelallotement','Hostel Allotement');
		$tab->addTabURL('hostel_allotedstudent','Alloted Student');
		$tab->addTabURL('hostel_studentgardian','Student Gardian');
		$tab->addTabURL('hostel_studentmovement','Student Movement');
		$tab->addTabURL('hostel_studentmovementreport','Student Movement Record');
		$tab->addTabURL('hostel_studentdisease','Student Diseases');
	}
}