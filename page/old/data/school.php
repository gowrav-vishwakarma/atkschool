<?php

        
class page_data_school extends Page
{
    
    function init()
    {
        parent::init();
         $tabs=$this->add('Tabs'); 
         $tabs->addTabURL('data_school_scholar',"Current Scholars");

         }
}

