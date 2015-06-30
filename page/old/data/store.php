<?php

class page_data_store extends Page{
    
    function init()
    {
        parent::init();
        $tab=$this->add('Tabs');
        $tab->addTabURL('data_store_allotement','Store No Allotement');
        $tab->addTabURL('data_store_inward','Item Inward');
        $tab->addTabURL('data_store_issue','Item Issue');
    }
}