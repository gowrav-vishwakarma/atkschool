<?php

class page_schooldata extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$tabs=$this->add('Tabs');
		$tabs->addtabURL('school_scholars','Sessions Students');
		$tabs->addtabURL('school_studentClassMapping','Student Class Association');
		$tabs->addtabURL('student_rollnoallotment','Roll Numbers');
		$tabs->addtabURL('student_attendance','Student Monthly Attendance');
		$tabs->addtabURL('student_marks','Student Marks Input');
		$tabs->addtabURL('student_classassign','Student Class Assign');
		$tabs->addtabURL('student_fee','Fee Deposite');
		// $tabs->addtabURL('student_status','Students Fee Status');
	}
}