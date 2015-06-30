<?php

class page_reports_hostel extends Page
{
    
    function init()
    {
        parent::init();
         $t=$this->add('Tabs'); 
         $t_stg=$t->addTabURL('reports_hostel_attendence','Attendance');
         $t_store=$t->addTabURL('reports_hostel_store','Store');
         $t_store=$t->addTabURL('reports_hostel_disease','Disease');
  
         }
}
