<?php

        
class page_data_hostel extends Page
{
    
    function init()
    {
        parent::init();
         
         $allot=$this->api->auth->model['d_h_allot'];
         $alloted=$this->api->auth->model['d_h_alloted'];
         $guardian=$this->api->auth->model['d_h_guardian'];
         $io=$this->api->auth->model['d_h_io'];
         $report=$this->api->auth->model['d_h_report'];
         $disease=$this->api->auth->model['d_h_disease'];
        //==================================================================================== 
         $t=$this->add('Tabs'); 
         if($allot==1)
             $t_allot=$t->addTabURL('data_hostel_allotment','Hostel Allotement');
         if($alloted==1)
             $t_ast = $t->addTabURL('data_hostel_allotedstudents','Alloted Student');
         if($guardian)
            $t_stg=$t->addTabURL('data_hostel_studentguardian','Student guardian');
         if($io==1)
            $t_io=$t->addTabURL('data_hostel_outward','Inward/Outward');
         if($report==1) 
            $t->addTabURL('data_hostel_report','Outward Report');
         if($disease==1)
            $t->addTabURL('data_hostel_disease','Disease');
         

         }
}

