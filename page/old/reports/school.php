<?php

class page_reports_school extends Page
{
    
    function init()
    {
        parent::init();
       $t=$this->add('Tabs'); 
        $t->addTabURL('./studentlistF','StudentList');
         
    }
}

