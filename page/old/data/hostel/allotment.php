<?php

class page_data_hostel_allotment extends Page
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
        $this->api->stickyGET('class');
        
        $g=$this->add('Grid');
        $query = $this->api->db->dsql()->expr("SELECT scholars_master.hname AS scholar, student.id, student.isalloted, class_master.`name` AS class FROM scholars_master, student, class_master WHERE student.scholar_id = scholars_master.id AND student.ishostler = TRUE AND student.class_id = class_master.id AND class_master.id LIKE '%".$_GET['name']."%' order by class_id,scholars_master.fname");
        
        
        $g->addColumn('template','scholar')->setTemplate('<div class="hindi"><?$scholar?></div>');
        $g->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
        $g->addColumn('boolean','isalloted');
        $g->addColumn('expander','allot');
        
        //$g->addCondition('ishostler',true);
//        if($_GET['name'])
//            $g->dq->where('class_id like ','%'.$_GET['name'].'%');
       
    if ($f->isSubmitted()) 
        {

           $g->js(null,
                   $g->js()->reload(array("name" => $f->get('class'))))->execute();
        }
        $g->setSource($query);
         
    }       
    function page_allot()
        {
       $p= $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
                 ->addStyle('background','#ddd');
        $this->api->stickyGET('id');
         $this->api->stickyGET('session_id');
        $this->api->stickyGET('room'); 
        $f=$p->add('Form');
        $drp_hstl=$f->addField('dropdown','hostel');
   
        $r=$this->add('Model_Hostel_Rooms');
        
        $drp_room=$f->addField('dropdown','room','Room Number');
       $drp_room->setModel($r);
         
        $hstl = array("%" => "Select Hostel");
         $m=$this->add('Model_Hostel');
        foreach ($m as $a) 
            {
             
                        
         $hstl+= array($a['id'] => $a['building_name']);
             }
        $drp_hstl->setValueList($hstl);
       $drp_hstl->js('change',$f->js()->atk4_form('reloadField','room',array($this->api->getDestinationURL(),'hostel_idx'=>$drp_hstl->js()->val())));        
      // $line= $f->addField('line','remaining');
        
       
      
        
       if($_GET['hostel_idx'])
        {
         $drp_room->dq
                 
                 ->where('hostel_id like ','%'.$_GET['hostel_idx'].'%');

        }    
        
        
    $f->addSubmit('Save');
         
    
       if($f->isSubmitted())
        {
              
       //--------------------------Retrive number of students alloted to a room in a session-----------------------------
        

          $countTemp=$this->add('Model_Hostel_Allotment'); 
          $count= $countTemp->getroomcount($f->get('room'));
          
          
    //---------------------------Retrive capacity of the room---------------------------------------------------
          
          global $capacity;
          $capacity = $countTemp->getcapacity($f->get('room'));
          $remaining=$capacity-$count-1;
          
  
           
          
    //--------------------------Checking for duplicacy of student in a session-------------------------------------
          
          if($countTemp->isAlloted($_GET['id']))
          {
             
              $this->js()->univ()->alert('Student has been already alloted a room')->execute();
              
             return;
          }
    //---------------------------------Check if room is full or not---------------------------------------------
          
          if($count>=$capacity)
          {
             $this->js()->univ()->alert('Room is already Full')->execute();
             return;
          }
         else 
             {
                $save=$this->add('Model_Hostel_Allotment');
                $save->set('room_id',$f->get('room'));
                $save->set('student_id',$_GET['id']);
                $save->save();
                $this->api->db->dsql()->expr('update student set student.isalloted=1 where id='.$_GET['id'])->execute();
              
                $this->js()->univ()->closeExpander()->successMessage("HOSTEL ALLOTED"." and remaining =".$remaining)->execute();
                
             }
        
        }
       
        
        
        }
        
}