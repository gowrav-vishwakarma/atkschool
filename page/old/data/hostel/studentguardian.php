<?php

class page_data_hostel_studentguardian extends Page {

    function initMainPage() {
        parent::init();
        $crud = $this->add('CRUD', array('allow_add' => false, 'allow_edit' => false, 'allow_del' => FALSE));
        $r=$this->add('Model_Student_Current');
        $r->addCondition('ishostler',true);
        $crud->setModel($r,array('scholar','class'));
        
        if ($crud->grid) {
            $crud->grid->addColumn('template','scholar')->setTemplate('<div class="hindi"><?$scholar?></div>');
            $crud->grid->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
            $crud->grid->addQuickSearch(array('class'));    
          $btn_add = $crud->grid->addColumn('Button', 'btn', 'Add Guardian');
          $btn_edit=$crud->grid->addColumn('expander','edit','Edit/Delete');
        }
        if ($_GET['btn']) {
             $bq=$this->api->db->dsql()->table('student')->field('scholar_id')->where('id='.$_GET['btn'])->do_getOne();         
           $this->js('click')->univ()->frameURL('Add Guardian', array($this->api->url('./add'), 'scholar_id' => $bq))->execute();
        }
        
    }

    function page_add() {
        $this->api->stickyGET('scholar_id');
        if(true) {
         $m= $this->setModel('Hostel_StudentGuardian');         
         $m->set('scholar_id',$_GET['scholar_id']);//$this->api->stickyGET('scholar_id'));        
         $m->getElement('scholar_id')->system(true);
         $f=$this->add('InfiniteAddForm');
         $f->setModel($m,null,array('gname','image_url','relation'));//,array('gname','image','relaxtion'));
         //$f->form->getAllFields()->set('class','hindi'); 
         $f->form->getElement('relation')->setAttr('class','hindi');
         $f->form->getElement('gname')->setAttr('class','hindi');
         $f->form->getElement('address')->setAttr('class','hindi');
        
       }          
        
    }

    function page_edit() {

        $this->api->stickyGET('student_id');
        $dq=$this->api->db->dsql()->table('student')->field('scholar_id')->where('id='.$_GET['student_id'])->do_getOne();
        $crud = $this->add('CRUD',array('allow_add'=>false))->addStyle('background','#ddd');
        
        $m=$this->add('Model_Hostel_StudentGuardian');
        $m->addCondition('scholar_id',$dq);
        $crud->setModel($m,array('gname','contact','relation','address','image'),array('Guardian','contact','relation'));
        if($crud->grid)
        {
            $crud->grid->addColumn('template','Guardian')->setTemplate('<div class="hindi"><?$Guardian?></div>');
            $crud->grid->addColumn('template','relation')->setTemplate('<div class="hindi"><?$relation?></div>');
        }
        
        if($crud->form)
        {
            $crud->form->getElement('gname')->setAttr('class','hindi');
            $crud->form->getElement('relation')->setAttr('class','hindi');
            $crud->form->getElement('address')->setAttr('class','hindi');
        }
    }

}