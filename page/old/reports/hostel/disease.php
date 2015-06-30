<?php

class page_reports_hostel_disease extends Page
{
    function init()
    {
        parent::init();
         $t=$this->add('Tabs'); 
        $t->addTabURL('reports_hostel_disease_diseaseF','Disease');
    }
}
