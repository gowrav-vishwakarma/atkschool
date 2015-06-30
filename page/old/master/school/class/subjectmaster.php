<?php

class page_master_school_class_subjectmaster extends Page
{
    
    function init()
    {
        
        parent::init();
         $crud_subject=$this->add('CRUD');
          
         $crud_subject->setModel('Subject');
        if($crud_subject->form)
         $crud_subject->form->getElement('name')->setAttr('class','hindi');
         if($crud_subject->grid)
         {
            $crud_subject->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
         }
    }
}