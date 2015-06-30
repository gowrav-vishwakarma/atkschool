<?php

class page_reports_hostel_attendence_classAttendence extends Page {

    function init() {
        parent::init();
        
        $g=$this->add('Grid');
        
        $query = $this->api->db->dsql()
                ->expr("SELECT faketable.class, faketable.total, IFNULL(faketable.present, 0) AS present, ifnull(( faketable.total - faketable.present ), faketable.total ) AS absent FROM ( SELECT CONCAT( class_master.`name`, \" \", class_master.section ) AS class, ptable.total, count( hosteller_outward.scholar_id ) AS present FROM hosteller_outward, student, hostel_allotement, rooms, hostel_master, class_master, ( SELECT class_master.id AS cid, count(hostel_allotement.id) AS total FROM class_master, hostel_allotement, student WHERE hostel_allotement.student_id = student.id AND student.class_id = class_master.id GROUP BY class_master.id ) AS ptable WHERE hosteller_outward.id IN ( SELECT MAX(id) AS id FROM hosteller_outward WHERE purpose NOT IN ('enquiry') GROUP BY scholar_id ) AND purpose IN ( 'inward', 'card inward', 'self inward' ) AND hosteller_outward.scholar_id = student.scholar_id AND hostel_allotement.student_id = student.id AND rooms.id = hostel_allotement.room_id AND hostel_master.id = rooms.hostel_id AND student.class_id = class_master.id AND ptable.cid = class_master.id GROUP BY student.class_id ) AS faketable UNION SELECT \"dqy\" AS class, sum(faketable1.total) AS total, sum(faketable1.present) AS present, ( sum(faketable1.total) - sum(faketable1.present)) AS absent FROM ( SELECT faketable.class, faketable.total, IFNULL(faketable.present, 0) AS present, ifnull(( faketable.total - faketable.present ), faketable.total ) AS absent FROM ( SELECT CONCAT( class_master.`name`,\" \", class_master.section ) AS class, ptable.total, count( hosteller_outward.scholar_id ) AS present FROM hosteller_outward, student, hostel_allotement, rooms, hostel_master, class_master, ( SELECT class_master.id AS cid, count(hostel_allotement.id) AS total FROM class_master, hostel_allotement, student WHERE hostel_allotement.student_id = student.id AND student.class_id = class_master.id GROUP BY class_master.id ) AS ptable WHERE hosteller_outward.id IN ( SELECT MAX(id) AS id FROM hosteller_outward WHERE purpose NOT IN ('enquiry') GROUP BY scholar_id ) AND purpose IN ( 'inward', 'card inward', 'self inward' ) AND hosteller_outward.scholar_id = student.scholar_id AND hostel_allotement.student_id = student.id AND rooms.id = hostel_allotement.room_id AND hostel_master.id = rooms.hostel_id AND student.class_id = class_master.id AND ptable.cid = class_master.id GROUP BY student.class_id ) AS faketable ) AS faketable1");
        $g->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
        $g->addColumn('text','total');
        $g->addColumn('text','present');
        $g->addColumn('text','absent');
        $g->setSource($query);
    }
}