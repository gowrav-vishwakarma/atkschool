<?php

class page_master_school_class_classmaster extends Page
{
    
    function  initMainPage()
    {
        
        parent::init();
         $crud_class=$this->add('CRUD');
        $crud_class->setModel('Class',array('class_name','section'));
        
        if($crud_class->form)
        {
            $crud_class->form->getElement('class_name')->setAttr('class','hindi');
            $crud_class->form->getElement('section')->setAttr('class','hindi');
        }
            
        
        if($crud_class->grid){
            $crud_class->grid->addColumn('expander','map','Edit Subjects');
            $crud_class->grid->addColumn('template','class_name')->setTemplate('<div class="hindi"><?$class_name?></div>');
           $crud_class->grid->addColumn('template','section')->setTemplate('<div class="hindi"><?$section?></div>');
            
        }
    }
    function page_map(){
        $p=$this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
        ->addStyle('background','#eee'); // bevel

        $this->api->stickyGET('class_master_id');
        $cl=$this->add('Model_Class')->load($_GET['class_master_id']);

        $g=$p->add('Grid');
        $g->setModel('Model_Subject');
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');

        $f=$p->add('Form');
        $sel=$f->addField('line','sel');
        $sel->js(true)->closest('.atk-form-row')->hide();

        $map=$cl->ref('SubjectClassMap');
  // fetches IDs
        $sel->set(json_encode($map->dsql()->del('field')->field('subject_id')->execute()->stmt->fetchAll(PDO::FETCH_COLUMN)));

        $g->addSelectable($sel);

        $f->addSubmit('Save');
        if($f->isSubmitted()){
            $this->api->db->beginTransaction();

            // delete old mappings
            $map->deleteAll();
            $cl->setSubjects(json_decode($f->get('sel')));

            $this->api->db->commit();
            $this->js()->univ()->closeExpander()->successMessage('Mapping saved')->execute();
        }
    
}
}