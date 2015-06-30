<?php

class page_reports_hostel_attendence_cattendence extends Page
{
    function init()
    {
        parent::init();
        
        $f=$this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
        $drp_class=$f->addField('dropdown','class')->setAttr('class', 'hindi');
        
         $cls = array("%" => "Select Class");
         $m=$this->add('Model_Class');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);
        
        $drp_status = $f->addField('dropdown','status');
        $drp_status->setValueList(array('%'=>'Select Status','inward'=>'inward','outward'=>'outward'));
        
   //=================================hostel dropdown=============================================================================
        $drp_hostel = $f->addField('dropdown', 'hostel');
        $hstl = array("%" => "Select Hostel");
        $h = $this->add('Model_Hostel');
        
        foreach ($h as $a) {
            $hstl+= array($a['id'] => $a['building_name']);
        }
        $drp_hostel->setValueList($hstl);
        
 //==============================room dropdown=============================================================================               
         $drp_room = $f->addField('dropdown', 'room');

        $room = $this->api->db->dsql()->expr("SELECT \"%\" AS id, \"Select Room\" AS `room_no` UNION SELECT rooms.id,room_no FROM rooms, hostel_master WHERE hostel_master.id = rooms.hostel_id AND  hostel_master.id LIKE '".$_GET['hostel_idx']."%'");
        $rm = array();
        foreach ($room as $a) {
            $rm+= array($a['id'] => $a['room_no']);
        }
        $drp_room->setValueList($rm);
       
        $drp_hostel->js('change', $f->js()->atk4_form('reloadField', 'room', array($this->api->getDestinationURL(), 'hostel_idx' => $drp_hostel->js()->val())));

        
        
        
        
        
        
        $f->addSubmit('List');
         $g=$this->add('Grid');
         
         $query = $this->api->db->dsql()->
                 expr("SELECT @a :=@a + 1 AS sn, `name`, class, hostel, room, `status` FROM (SELECT @a := 0) AS a, ( SELECT scholars_master.hname AS `name`, CONCAT( class_master.`name`, \" \", class_master.section ) AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, hosteller_outward.purpose AS `status` FROM scholars_master, class_master, hosteller_outward, student, hostel_master, rooms, hostel_allotement WHERE scholars_master.id = student.scholar_id AND class_master.id LIKE '%".$_GET['name']."%' and rooms.id LIKE '%".$_GET['room_id']."%' and hostel_master.id LIKE '%".$_GET['hostel_id']."%' AND student.class_id = class_master.id AND hosteller_outward.scholar_id = scholars_master.id AND hosteller_outward.purpose LIKE '%".$_GET['status']."%' AND hostel_allotement.student_id = student.id AND hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id AND hosteller_outward.id IN ( SELECT MAX(id) FROM hosteller_outward where id not in( select id from hosteller_outward where hosteller_outward.purpose='enquiry') GROUP BY hosteller_outward.scholar_id ) ORDER BY hostel_master.building_name, rooms.room_no, class_master.id ) AS faketable");
          
         
         
         if ($f->isSubmitted()) 
        {

           $g->js(null,
                   $g->js()->reload(array("name" => $f->get('class'),"status"=>$f->get('status'),"room_id"=>$f->get('room'),"hostel_id"=>$f->get('hostel'))))->execute();
           
        }
        
        $g->addColumn('text','sn');
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        $g->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
        $g->addColumn('text','hostel');
        $g->addColumn('text','room');
        $g->addColumn('template','status')->setTemplate('<div > <span class="<?$status?>"><?$status?></span></div>');
        $g->setsource($query);
    }
}
