<?php

class page_reports_hostel_attendence_hattendence extends Page {

    function init() {
        parent::init();
        
        $g=$this->add('Grid');
        
        $query = $this->api->db->dsql()
                ->expr("SELECT faketable.hostel, faketable.total, IFNULL(faketable.present, 0) AS present, ifnull(( faketable.total - faketable.present ), faketable.total ) AS absent FROM ( SELECT hostel_master.building_name AS hostel, count( hostel_allotement.student_id ) AS total, presenttable.present FROM hostel_allotement, rooms, hostel_master LEFT JOIN ( SELECT hostel_master.id AS hmid, count( hosteller_outward.scholar_id ) AS present FROM hosteller_outward, student, hostel_allotement, rooms, hostel_master WHERE hosteller_outward.id IN ( SELECT MAX(id) AS id FROM hosteller_outward WHERE purpose NOT IN ('enquiry') GROUP BY scholar_id ) AND purpose IN ( 'inward', 'card inward', 'self inward' ) AND hosteller_outward.scholar_id = student.scholar_id AND hostel_allotement.student_id = student.id AND rooms.id = hostel_allotement.room_id AND hostel_master.id = rooms.hostel_id GROUP BY rooms.hostel_id ) AS presenttable ON hostel_master.id = presenttable.hmid WHERE hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id GROUP BY rooms.hostel_id ) AS faketable UNION SELECT \"TOTAL\" AS hostel, sum(faketable1.total) AS total, sum(faketable1.present) AS present, ( sum(faketable1.total) - sum(faketable1.present)) AS absent FROM ( SELECT faketable.hostel, faketable.total, faketable.present, ( faketable.total - faketable.present ) AS absent FROM ( SELECT hostel_master.building_name AS hostel, count( hostel_allotement.student_id ) AS total, presenttable.present FROM hostel_allotement, rooms, hostel_master LEFT JOIN ( SELECT hostel_master.id AS hmid, count( hosteller_outward.scholar_id ) AS present FROM hosteller_outward, student, hostel_allotement, rooms, hostel_master WHERE hosteller_outward.id IN ( SELECT MAX(id) AS id FROM hosteller_outward WHERE purpose NOT IN ('enquiry') GROUP BY scholar_id ) AND purpose IN ('inward') AND hosteller_outward.scholar_id = student.scholar_id AND hostel_allotement.student_id = student.id AND rooms.id = hostel_allotement.room_id AND hostel_master.id = rooms.hostel_id GROUP BY rooms.hostel_id ) AS presenttable ON hostel_master.id = presenttable.hmid WHERE hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id GROUP BY rooms.hostel_id ) AS faketable ) AS faketable1");
        $g->addColumn('text','hostel');
        $g->addColumn('text','total');
        $g->addColumn('text','present');
        $g->addColumn('text','absent');
        $g->setSource($query);
    }
}