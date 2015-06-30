<?php

class page_data_hostel_report extends Page {

    function initMainPage() {
        parent::init();
        
        $f = $this->add('Form');
//=====================================class dropdown======================================================================
        $drp_class = $f->addField('dropdown', 'class');
        $cls = array("%" => "p;u");
        $m = $this->add('Model_Class');
        $f->getElement('class')->setAttr('class', 'hindi');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);

//=================================scholar dropdown================================================================================      
        $drp_std = $f->addField('dropdown', 'scholar');

        $r = $this->api->db->dsql()->expr("(SELECT scholars_master.id, `hname` as `name` FROM scholars_master, class_master, session_master, student WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND class_master.id LIKE '%".$_GET['class_idx']."%' AND session_master.iscurrent = TRUE and student.ishostler=true order by scholars_master.fname)");
      $cls = array("%" => "p;u");
        foreach ($r as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_std->setValueList($cls);
        $f->getElement('scholar')->setAttr('class', 'hindi');
        $drp_class->js('change', $f->js()->atk4_form('reloadField', 'scholar', array($this->api->getDestinationURL(), 'class_idx' => $drp_class->js()->val())));
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
//==============================Action dropdown============================================================================
          $drp_io = $f->addField('dropdown', 'action');
          $drp_io->setValueList(array("%"=>"Select Action","inward"=>"inward","outward"=>"outward"));
          
          $f->addField('DatePicker','from','From Date');
          $f->addField('DatePicker','to','To Date');
          
          $f->addClass('stacked atk-row');
 
// strategical placement of atk-row and spanX
$f->template->trySet('fieldset','span3');
$f->add('Order')
    ->move($f->addSeparator('span3'),'before','hostel')
    ->move($f->addSeparator('span6'),'before','from')
    ->now();
          
        $f->addSubmit('Generate');
        
                          
         if ($f->isSubmitted()) 
        {
             $to=$f->get('to');
             if($to=='')
             {
                 $xx = date('Y-m-d');
                 $to_date=date('Y-m-d',strtotime("$xx, +1 day"));    
             }
               else 
                    $to_date=date('Y-m-d',strtotime("$to, +1 day"));
              
             $from=$f->get('from');
             if($from=='')
               $from_date='2011-01-01';    
             else 
              $from_date=$from;
        
             $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./list'),'scholar_id'=>$f->get('scholar'),'class_id'=>$f->get('class'),'hostel_id'=>$f->get('hostel'),'room_id'=>$f->get('room'),'action'=>$f->get('action'),'to'=>$to_date,'from'=>$from_date))->execute(); 
        }
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./list'))->set('Loading..');
    }
    
    function page_list()
    {
        $this->api->stickyGET('scholar_id');
        $this->api->stickyGET('class_id');
        $this->api->stickyGET('hostel_id');
        $this->api->stickyGET('room_id');
        $this->api->stickyGET('action_id');
        $this->api->stickyGET('to');
        $this->api->stickyGET('from');
   //=====================================
        //echo  $_GET['scholar_id']." ".$_GET['class_id']." ".$_GET['hostel_id']." ".$_GET['room_id']." ".$_GET['action_id']." ".$_GET['to']." ".$_GET['from'];
        if($_GET['scholar_id'])
        {
            $q = $this->api->db->dsql()->expr("SELECT hosteller_outward.id as id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, scholar_guardian.gname AS guardian, hosteller_outward.purpose AS action, hosteller_outward.date AS date,hosteller_outward.remark AS remarks FROM scholars_master, class_master, hostel_master, rooms, student, hostel_allotement, hosteller_outward LEFT OUTER JOIN scholar_guardian ON scholar_guardian.id = hosteller_outward.withid WHERE scholars_master.id like '".$_GET['scholar_id']."%' and class_master.id like '".$_GET['class_id']."%' and hostel_master.id like '".$_GET['hostel_id']."%' and rooms.id like '".$_GET['room_id']."%' and hosteller_outward.purpose like '%".$_GET['action']."%' AND hosteller_outward.date between '".$_GET['from']."' and '".$_GET['to']."' AND class_master.id = student.class_id AND rooms.hostel_id = hostel_master.id AND hostel_allotement.student_id = student.id AND scholars_master.id = student.scholar_id AND hostel_allotement.room_id = rooms.id AND hosteller_outward.scholar_id = scholars_master.id order by date desc");
        }
        
        else
        {
         $q = $this->api->db->dsql()->expr("SELECT hosteller_outward.id as id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, scholar_guardian.gname AS guardian, hosteller_outward.purpose AS action, hosteller_outward.date AS date,hosteller_outward.remark AS remarks FROM scholars_master, class_master, hostel_master, rooms, student, hostel_allotement, hosteller_outward LEFT OUTER JOIN scholar_guardian ON scholar_guardian.id = hosteller_outward.withid WHERE scholars_master.id like '".$_GET['scholar_id']."%' and class_master.id like '".$_GET['class_id']."%' and hostel_master.id like '".$_GET['hostel_id']."%' and rooms.id like '".$_GET['room_id']."%' and hosteller_outward.purpose like '%".$_GET['action']."%' AND class_master.id = student.class_id AND rooms.hostel_id = hostel_master.id AND hostel_allotement.student_id = student.id AND scholars_master.id = student.scholar_id AND hostel_allotement.room_id = rooms.id AND hosteller_outward.scholar_id = scholars_master.id order by date desc");

        }
        
            $g = $this->add('Grid');
            $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
            $g->addColumn('text','class');
            $g->addColumn('text','hostel');
            $g->addColumn('text','room');
            $g->addColumn('template','guardian')->setTemplate('<div class="hindi"><?$guardian?></div>');
            $g->addColumn('text','action');
            $g->addColumn('datetime','date');
            $g->addColumn('text','remarks');
            $g->addColumn('confirm','delete');
            $g->setSource($q);
            
            if($_GET['delete'])
            {
                //$this->js()->univ()->successMessage($_GET['delete'])->execute();
                $this->api->db->dsql()->table('hosteller_outward')->where('id ='.$_GET['delete'])->delete();
                $this->js(null,$g->js()->reload())->univ()->successMessage("Deleted")->execute();
            }
            
            
          //  $this->js()->univ()->successMessage($f->get('scholar'))->execute();
        }
    
        
    
}