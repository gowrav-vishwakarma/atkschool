<?php

class page_data extends Page
{
    
    function init()
    {
        parent::init();
         $tabs=$this->add('Tabs'); 
         $school=$this->api->auth->model['d_school'];
          $staff=$this->api->auth->model['d_staff'];
          $hostel=$this->api->auth->model['d_hostel'];
        if($school==1)
                  $tabs->addTabURL('data_school',"School");
        if($hostel==1)
                  $tabs->addTabURL('data_hostel',"Hostel");
        if($staff==1)
                  $tabs->addTabURL('data_staff',"staff");
              
        $tabs->addTabURL('data_store','store');
        
    }
}