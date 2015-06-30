<?php
class page_master_store extends Page {

     function init() {
        parent::init();
        $tabs=$this->add('tabs');
        $tabs->addTabURL('master_store_item','Item Master');
        $tabs->addTabURL('master_store_party','Party Master');
        
    }
}