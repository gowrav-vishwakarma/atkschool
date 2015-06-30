<?php

class page_master_hostel_addhostel extends Page
{
    function initMainPage()
    {
        parent::init();
         $crud_build= $this->add('CRUD');
       $crud_build->setModel('Hostel');
       if($crud_build->grid)
       {
            $crud_build->grid->addColumn('expander','room','Add Rooms');
             
            
       }
      
    }
     function page_room()
    {
//        $p=$this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
//                 ->addStyle('background','#ddd'); // bevel
        $this->api->stickyGET('hostel_master_id');
        $t=$this->add('Model_Hostel_Rooms');
        $t->addCondition('hostel_id',$_GET['hostel_master_id']);
        $g=$this->add('CRUD')->addStyle('background','#ddd');
        $g->setModel($t,array('room_no','capacity','alloted','vacant'));

        //$g->setSource('Hostel_Rooms');

    }
   
    
}