<?php

class page_reports_hostel_store extends Page
{
    function init()
    {
        parent::init();
         $t=$this->add('Tabs'); 
          $t->addTabURL('reports_hostel_store_storeNumberListF','Store Number List');
          $t->addTabURL('reports_hostel_store_recieptF','Receipt');
    }
}
