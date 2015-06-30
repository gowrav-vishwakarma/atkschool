<?php

class page_master_hostel extends Page
{
    function init()
    {
        parent::init();
    
         $t=$this->add('Tabs');
         $t_build=$t->addTabURL('master_hostel_addhostel','Add Hostel');

     
      
    }
    
    
}