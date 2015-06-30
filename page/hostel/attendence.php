<?php

class page_hostel_attendence extends Page{
	function page_index(){
		// parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$tabs=$this->add('Tabs');
		$tabs->addTabURL('./roomvise','Rooms Attendance');
		$tabs->addTabURL('./classvise','Class Attendance');
		$tabs->addTabURL('./hostelvise','Hostel Attendance');
		$tabs->addTabURL('hostel/attendancereport','Attendance Report');
		
	}

	function page_roomvise(){
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');

		$session=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
		
		$q="
		SELECT
			building_name,
			room_no,
			attendance,
			totalstudent,
			totalstudent - attendance absent
		FROM
		(
			SELECT 
			hm.building_name building_name,
			rm.room_no room_no,
			sum(s.is_present) attendance,
			count(s.id) totalstudent
			FROM
				hostel_allotement rmalot
				join student s on s.id=rmalot.student_id
				join rooms rm on rmalot.room_id=rm.id
				join hostel_master hm on hm.id=rm.hostel_id
			WHERE
				rmalot.session_id=$session
			GROUP BY building_name, room_no
			) tmp
		";
		$q=$this->api->db->dsql()->expr($q);

		$grid->setSource($q);
		$grid->addColumn('text','building_name');
		$grid->addColumn('text','room_no');
		$grid->addColumn('number','totalstudent');
		$grid->addColumn('number','attendance');
		$grid->addColumn('number','absent');

		$grid->addTotals(array('attendance','absent','totalstudent'));
	}

	function page_classvise(){
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');

		$session=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
		
		$q="
			SELECT
				class,
				attendance,
				totalstudent,
				totalstudent - attendance absent


			FROM
			(SELECT 
			concat(c.name,' ',c.section) class,
			sum(s.is_present) attendance,
			count(s.id) totalstudent
			FROM
				hostel_allotement rmalot
				join student s on s.id=rmalot.student_id
				join class_master c on s.class_id=c.id
			WHERE
				rmalot.session_id=$session
			GROUP BY s.class_id
			) tmp
		";
		$q=$this->api->db->dsql()->expr($q);

		$grid->setSource($q);
		$grid->addColumn('text','class');
		$grid->addColumn('number','totalstudent');
		$grid->addColumn('number','attendance');
		$grid->addColumn('number','absent');

		$grid->addFormatter('class','hindi');
		$grid->addTotals(array('absent','attendance','totalstudent'));
	}

	function page_hostelvise(){
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');

		$session=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
		
		$q="
			SELECT
				building_name,
				attendance,
				totalstudent,
				totalstudent - attendance absent

			FROM
			(SELECT 
			hm.building_name building_name,
			sum(s.is_present) attendance,
			count(s.id) totalstudent
			FROM
				hostel_allotement rmalot
				join student s on s.id=rmalot.student_id
				join rooms rm on rmalot.room_id=rm.id
				join hostel_master hm on hm.id=rm.hostel_id
			WHERE
				rmalot.session_id=$session
			GROUP BY building_name
			) tmp
		";
		$q=$this->api->db->dsql()->expr($q);

		$grid->setSource($q);
		$grid->addColumn('text','building_name');
		$grid->addColumn('number','totalstudent');
		$grid->addColumn('number','attendance');
		$grid->addColumn('number','absent');

		$grid->addTotals(array('absent','totalstudent','attendance'));

	}
}