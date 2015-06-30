<?php

class page_reports extends Page
{
    
    function init()
    {
        parent::init();
         $school=$this->api->auth->model['r_school'];
         $hostel=$this->api->auth->model['r_hostel'];
         //======================================================================
         $tabs=$this->add('Tabs'); 
         
         if($school==1)
             $tabs->addTabURL('reports_school',"School");
         
         if($hostel==1) 
             $tabs->addTabURL('reports_hostel',"Hostel");
         
    }
}
