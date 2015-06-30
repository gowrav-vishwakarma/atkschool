<?php
class page_master_store_party extends Page {

     function init() {
        parent::init();
        $crud=$this->add('CRUD');
        $crud->setModel('Store_Party');
        if($crud->grid)
        {
            $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
            $crud->grid->addColumn('template','address')->setTemplate('<div class="hindi"><?$address?></div>');
        }
        if($crud->form)
        {
            $crud->form->getElement('name')->setAttr('class','hindi');
            $crud->form->getElement('address')->setAttr('class','hindi');            
        }
    }
}