<?php

class page_master_school_class_exammaster extends Page
{
    function initMainPage()
    {
        parent::init();
        $crud=$this->add('CRUD');
        $crud->setModel('Exam');
        if($crud->form)
        {
            $crud->form->getElement('name')->setAttr('class','hindi');
        }
        if($crud->grid)
        {
            $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
            $crud->grid->addColumn('expander','clss','class');
            $crud->grid->addColumn('button','btn','subject');
            $crud->grid->addButton('Max-Min Marks')->js('click')->univ()->frameURL('Max-Min Marks', $this->api->url('master_school_class_maxmin'));

        }
        if ($_GET['btn']) {
             $bq=$this->api->db->dsql()->table('exam_master')->field('id')->where('id='.$_GET['btn'])->do_getOne();         
           $this->js('click')->univ()->frameURL('Add subjects', array($this->api->url('master/school/class/subMap'), 'exam_id' => $bq))->execute();
        }
    }
    function page_clss()
    {
       
        $p=$this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
        ->addStyle('background','#eee'); // bevel

        $this->api->stickyGET('exam_master_id');
        $cl=$this->add('Model_Exam')->load($_GET['exam_master_id']);

        $g=$p->add('Grid');
        $g->setModel('Model_Class',array('name'));
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        
        $f=$p->add('Form');
        $sel=$f->addField('line','sel');
        $sel->js(true)->closest('.atk-form-row')->hide();

        $map=$cl->ref('ExamMap');
  // fetches IDs
        $sel->set(json_encode($map->dsql()->del('field')->field('class_id')->execute()->stmt->fetchAll(PDO::FETCH_COLUMN)));

        $g->addSelectable($sel);

        $f->addSubmit('Save');
        if($f->isSubmitted()){
            
           // $this->js()->univ()->successMessage(json_decode($f->get('sel')))->execute();
          //  $this->api->db->beginTransaction();

            // delete old mappings
            //$map->deleteAll();
            $cl->setClass(json_decode($f->get('sel')));
           
           // $this->api->db->commit();
            $this->js()->univ()->closeExpander()->successMessage('Mapping saved')->execute();
        }
    }
    
    
}