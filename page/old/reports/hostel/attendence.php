<?php

class page_reports_hostel_attendence extends Page
{
    function init()
    {
        parent::init();
        
        $t=$this->add('Tabs');
        $t->addTabURL('reports_hostel_attendence_hattendence','Total Hostel');
        $t->addTabURL('reports_hostel_attendence_classAttendence','Total Class');
        $t->addTabURL('reports_hostel_attendence_cattendence','Class Wise');
        
    }
}