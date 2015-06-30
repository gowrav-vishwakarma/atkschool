<?php

class page_data_staff_addstaff extends Page
{
 function init()
    {
        parent::init();
        
        $c = $this->add('CRUD');
        $m=$this->add('Model_Staff');
        $c->setModel('Staff',null, array('name','designation','contact'));
        if($c->form){
            // make form flow in 2 columns
            $c->form->setFormClass('stacked atk-row');
            $o=$c->form->add('Order')
                ->move($c->form->addSeparator('noborder span6'),'first')
                ->move($c->form->addSeparator('noborder span5'),'middle')
                ->now();
            
            $c->form->getElement('hname')->setAttr('class','hindi');
            $c->form->getElement('designation')->setAttr('class','hindi');
            $c->form->getElement('father_name')->setAttr('class','hindi');
            $c->form->getElement('mother_name')->setAttr('class','hindi');
            $c->form->getElement('guardian_name')->setAttr('class','hindi');
            $c->form->getElement('address')->setAttr('class','hindi');
        }
        
        if ($c->grid) {
   
            $q = $this->api->db->dsql()->expr('SELECT filestore_file.filename as image FROM staff_master, filestore_file WHERE filestore_file.id = staff_master.image');
            $c->grid->addPaginator(15);
            $c->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
            $c->grid->addColumn('template','designation')->setTemplate('<div class="hindi"><?$designation?></div>');
            $c->grid->addColumn('text','contact');
            
            
        }
    }
}