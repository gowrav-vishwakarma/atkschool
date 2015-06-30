<?php
class page_master_store_item extends Page {

     function init() {
        parent::init();
        $crud=$this->add('CRUD');
        $crud->setModel('Store_Item');
        if($crud->grid)
        {
           $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
           $crud->grid->addColumn('template','category')->setTemplate('<div class="hindi"><?$category?></div>');
            $crud->grid->addQuickSearch(array('category'),'QuickSearch');
        }
        if($crud->form)
        {
            $crud->form->getElement('name')->setAttr('class','hindi');
            $crud->form->getElement('category')->setAttr('class','hindi');
           
            
        }
    }
}