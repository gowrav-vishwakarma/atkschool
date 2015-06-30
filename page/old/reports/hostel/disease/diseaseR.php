<?php

class page_reports_hostel_disease_diseaseR extends Page
{
     
    function init()
    {
        parent::init();
    
      $this->api->stickyGET('class');
      $this->api->stickyGET('scholar');
   
     $b = $this->api->db->dsql()->expr("SELECT faketable.*, @sn :=@sn + 1 AS sn FROM ( SELECT disease_master.id AS id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM scholars_master, class_master, hostel_master, rooms, disease_master, student LEFT OUTER JOIN hostel_allotement ON hostel_allotement.student_id = student.id WHERE scholars_master.id = student.scholar_id AND scholars_master.id like '".$_GET['scholar']."%' AND student.class_id = class_master.id AND student.class_id like '".$_GET['class']."%' AND hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id AND disease_master.scholar_id = scholars_master.id UNION SELECT disease_master.id AS id, scholars_master.fname AS `name`, class_master.`name` AS class, \"\" AS hostel, \"\" AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM scholars_master, class_master, disease_master, student WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND scholars_master.id like '".$_GET['scholar']."%' AND student.class_id like '".$_GET['class']."%' AND student.id NOT IN ( SELECT hostel_allotement.student_id FROM hostel_allotement ) AND disease_master.scholar_id = scholars_master.id ) AS faketable, (SELECT @sn := 0) AS sn ORDER BY faketable.report DESC");  
        $g= $this->add('Grid');
        $g->addColumn('text','sn');
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        $g->addColumn('text','class');
        $g->addColumn('text','hostel');
        $g->addColumn('text','room');
        $g->addColumn('template','disease')->setTemplate('<div class="hindi"><?$disease?></div>');
        $g->addColumn('datetime','report');
        $g->addColumn('boolean','treatment');
        $g->setSource($b); 
 
       
      
      
    
}
 
    function render(){
        $this->api->template->del("Menu");
        $this->api->template->del("logo");
        $this->api->template->trySet("Content","")  ;
        $this->api->template->trySet("Footer","")  ;
        
        parent::render();
    }
}