<?php

class page_data_staff extends Page
{
 function init()
    {
        parent::init();
     
        $staffMaster=$this->api->auth->model['d_s_add'];
        $io=$this->api->auth->model['d_s_io'];
        $report=$this->api->auth->model['d_s_report'];
        $attendence=$this->api->auth->model['d_s_attendence'];  
        //======================================================
        $t=$this->add('Tabs');
        if($staffMaster==1)
            $t->addTabURL('data_staff_addstaff','Add Staff');
        if($io==1)
            $t->addTabURL('data_staff_outward','Inward/Outward');
        if($report==1)
            $t->addTabURL('data_staff_report','Outward Report');
        if($attendence==1)
            $t->addTabURL('data_staff_attendence','Attendence');
    }
}