<?php


class page_master_school_fee_head extends Page
{
    
    function init()
    {
        
        parent::init();
        
        $crud=$this->add('CRUD');
        $crud->setModel('Feehead');
    }
}
