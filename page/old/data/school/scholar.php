<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    
class page_data_school_scholar extends Page {

    function initMainpage() {
        parent::init();
        // $crud=$this->add('CRUD',array('allow_add'=>false));
 try
        {
                 
 $crud = $this->add('Scholar_CRUD');
        
 $m = $this->add('Model_Scholar_Current');
           
        $crud->setModel($m, null, array('sn','name','Student_name', 'scholar_no', 'class'));
        if($crud->form){
            // make form flow in 2 columns
            $crud->form->setFormClass('stacked atk-row');
            $o=$crud->form->add('Order')
                ->move($crud->form->addSeparator('noborder span6'),'first')
                ->move($crud->form->addSeparator('noborder span5'),'middle')
                ->now();
            
            $crud->form->getElement('hname')->setAttr('class','hindi');
            $crud->form->getElement('father_name')->setAttr('class','hindi');
            $crud->form->getElement('mother_name')->setAttr('class','hindi');
            $crud->form->getElement('guardian_name')->setAttr('class','hindi');
            $crud->form->getElement('p_address')->setAttr('class','hindi');
            $crud->form->getElement('class_id')->setAttr('class','hindi');
//            $drp_cat=$crud->form->addField('dropdown','categary');
//            $cat=array("ST"=>"ST","SC"=>"SC","OBC"=>"OBC");
//            $drp_cat->setValueList($cat);
        }

        if ($crud->grid) {
            $crud->grid->addQuickSearch(array('scholar_no'));
            //$crud->grid->addButton('Enroll Existing scholar in This Session')->js('click')->univ()->frameURL('Associate Existing Student For Current Session', $this->api->url('./enroll'));
         //   $crud->grid->addColumn('expander', 'master_scholar_details');
           //$crud->grid->addQuickSearch(array('name'),'QuickSearch');
            
            $crud->grid->addPaginator(25);
            $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
            $crud->grid->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
            
            $crud->grid->getColumn('name')->makeSortable('fname');
            //$crud->grid->addTotals();
        }
 }
    
 catch (Exception $e)
 {
     $this->js()->univ()->alert(" Please Select Class ")->execute();  
 }
    }
    function page_master_scholar_details() {
//     $f= $this->add('Form');
//      $f->setModel('Student',array(''));
   
     
        
        
        
        /*   $m = $this->add('Model_Scholar_Current');
        $m->addCondition('session_id', $this->add('Model_Session_Current')->dsql()->field('id'));
//        $this->add('H1')->set("This is Mazor ... How to add/edit current class for this scholar for CURRENT SESSION HERE");
        $f = $this->add('MVCForm');
        $f->setModel($m)->load($_GET['scholars_master_id']);
        $f->addSubmit("Update Scholars details");
        if ($f->isSubmitted()) {
            $f->update();
        }
//        $crud=$this->add('CRUD');
//        $crud->setModel('Student');  */
    }

    function page_enroll() {

        $m = $this->add('Model_Student');
        $m->hasOne('Session', 'session_id');
        $m->addCondition('session_id', $this->add('Model_Session_Current')->dsql()->field('id')->getOne());
      
        $f = $this->add('Form');

        $f->setModel($m);
        $f->addSubmit();
        if ($f->isSubmitted())
            $f->update()->js()->univ()->successMessage('Enrolled')->closeDialog()->execute();
    }
}