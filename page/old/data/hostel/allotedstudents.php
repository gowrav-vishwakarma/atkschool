<?php

class page_data_hostel_allotedstudents extends Page
{
 function initMainPage()
    {
        parent::init();
        $f=$this->add('Form');
        $drp_class=$f->addField('dropdown','class')->setAttr('class', 'hindi');
        
         $cls = array("%" => "p;u");
         $m=$this->add('Model_Class');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);
        
        $f->addSubmit('List Students');
        //$this->api->stickyGET('class');
         global $crud_hs;
         $crud_hs=$this->add('Grid');//,array('allow_add' => false,'allow_del' => false,'allow_edit'=>false));
          
    //===============================Query Based=====================================================================
    
    
         $query = $this->api->db->dsql()->
                 expr("SELECT @a :=@a + 1 AS sn, faketable.id, faketable.`name`, faketable.class, faketable.building, faketable.room FROM (SELECT @a := 0) AS a, ( SELECT hostel_allotement.id AS id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS building, rooms.room_no AS room FROM hostel_allotement, scholars_master, class_master, hostel_master, rooms, student WHERE student.id = hostel_allotement.student_id AND scholars_master.id = student.scholar_id AND rooms.id = hostel_allotement.room_id AND rooms.hostel_id = hostel_master.id AND student.class_id = class_master.id AND class_master.id LIKE '%".$_GET['name']."%' ORDER BY building_name, room_no, class_master.`name`, scholars_master.fname ) AS faketable");
    
    
    
    if ($f->isSubmitted()) 
        {

           $crud_hs->js(null,
                   $crud_hs->js()->reload(array("name" => $f->get('class'))))->execute();
           
        }
         $crud_hs->addColumn('text','sn');
         $crud_hs->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
         $crud_hs->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
         $crud_hs->addColumn('text','building');
         $crud_hs->addColumn('text','room');
         
         
   //================================================================================================================
        
  
    /* ==================================Model Based (Old)=================================================================
     
     
        $crud_hs->setModel('Hostel_HostelStudents',array('Student_name','name','building_name','room'));//,array('hname','building_name','room')
        $crud_hs->addColumn('template','Student_name')->setTemplate('<div class="hindi"><?$Student_name?></div>');              
        
   
     ====================================================================================================================*/
       
//       $crud_hs->js(true)->addClass('reloadable');
//       $crud_hs->js('mygridload')->reload();      
       $crud_hs->addColumn('Button','edit');
       $crud_hs->addColumn('confirm',"delete");
       $crud_hs->setSource($query);
          if($_GET['delete'])
        {  
         
           $bq=$this->api->db->dsql()->expr('SELECT count(*) FROM hosteller_outward, hostel_allotement, student WHERE student.scholar_id = hosteller_outward.scholar_id AND student.id = hostel_allotement.student_id AND hostel_allotement.id ='.$_GET['delete'])->do_getOne();
           if($bq>0)
           {
               $stid=$this->api->db->dsql()->expr('select hostel_allotement.student_id from hostel_allotement where hostel_allotement.id='.$_GET['delete'])->do_getOne();
               $this->api->db->dsql()->expr('update student set student.isalloted=null where id='.$stid)->execute();
               $this->api->db->dsql()->table('hostel_allotement')->where('id ='.$_GET['delete'])->delete();
              $this->js(null,$crud_hs->js()->reload())->univ()->alert("Deleted Successfully but student has entries for Inward/Outward ")->execute();  
              // $this->js()->univ()->alert('Student has entries for Inward/Outward')->execute();
           }
           else
           {
               $stid=$this->api->db->dsql()->expr('select hostel_allotement.student_id from hostel_allotement where hostel_allotement.id='.$_GET['delete'])->do_getOne();
               $this->api->db->dsql()->expr('update student set student.isalloted=null where id='.$stid)->execute();
               $this->api->db->dsql()->table('hostel_allotement')->where('id ='.$_GET['delete'])->delete();
               
              $this->js(null,$crud_hs->js()->reload())->univ()->successMessage("Deleted")->execute();  
           }
               
           
                          
            
        }
        if($_GET['edit'])
        {
           $st_id= $this->api->db->dsql()->table('hostel_allotement')->field('student_id')->where('id ='.$_GET['edit'])->getOne();
            $rm_id= $this->api->db->dsql()->table('hostel_allotement')->field('room_id')->where('id ='.$_GET['edit'])->getOne();       
           $ssn_id= $this->api->db->dsql()->table('hostel_allotement')->field('session_id')->where('id ='.$_GET['edit'])->getOne();
        
           $this->js('click')->univ()->frameURL('Edit', array($this->api->url('./edit'), 'student_id' => $st_id,'room_id'=>$rm_id,'session_id'=>$ssn_id,'idx'=>$_GET['edit']))->execute();
           
           $this->js(null,$crud_hs->js()->reload())->univ()->successMessage("returned")->execute();
        }
      
       //$this->js(null,$crud_hs->js()->reload())->reload()->execute();  
      //$crud_hs->js()->reload()->execute();
    }
    function page_edit()
    {
        $this->api->stickyGET('idx');       
        $this->api->stickyGET('student_id');
        $this->api->stickyGET('room_id');
        $this->api->stickyGET('session_id');
        $ht_id= $this->api->db->dsql()->table('rooms')->field('hostel_id')->where('id ='.$_GET['room_id'])->getOne();
        $f=$this->add('Form');
        $drp_hsl=$f->addField('dropdown','hostel');
        $drp_rm=$f->addField('dropdown','room');
        
       
        
        $m=$this->add('Model_Hostel');
        $hsl = array("%" => "Select Hostel");
        foreach ($m as $a) {
            $hsl+= array($a['id'] => $a['building_name']);
        }
        $drp_hsl->setValueList($hsl);
        
        $r=$this->add('Model_Hostel_Rooms');

        $drp_rm->setModel($r,array('room_no'));
        
        $drp_hsl->js('change',$f->js()->atk4_form('reloadField','room',array($this->api->getDestinationURL(),'hostel_idx'=>$drp_hsl->js()->val())));        
      
       
       
      
        
       if($_GET['hostel_idx'])
        {
         $drp_rm->dq
                 
                 ->where('hostel_id like ','%'.$_GET['hostel_idx'].'%');

        }    
        
        $f->addSubmit();
        if($f->isSubmitted())
        {
           $val=$this->api->db->dsql()->table('rooms')->field('id')->where('id',$f->get('room'))->getOne(); 
           
    //--------------------------Retrive number of students alloted to a room in a session-----------------------------
        

          $countTemp=$this->add('Model_Hostel_Allotment'); 
          $count= $countTemp->getroomcount($f->get('room'));
          
          
    //---------------------------Retrive capacity of the room---------------------------------------------------
          
          global $capacity;
          $capacity = $countTemp->getcapacity($f->get('room'));
          $remaining=$capacity-$count-1;

          
    //---------------------------------Check if room is full or not---------------------------------------------
          
          if($count>=$capacity)
          {
             $this->js()->univ()->alert('Room is already Full')->execute();
             return;
          }
         else         
         {
           $m=$this->api->db->dsql()->table('hostel_allotement')->set('room_id',$val)->where('id',$_GET['idx'])->do_update();   
           //$this->js()->univ()->closeDialog()->execute();
           $this->js()->univ()->successMessage("Room updated successfully")->closeDialog()->execute();
           
         }
         
        }
       
        
        
        
    }
}
