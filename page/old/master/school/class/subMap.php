<?php

class page_master_school_class_subMap extends  Page
{
    function initMainPage()
    {
        parent::init();
        $this->api->stickyGET('exam_id');
       // $this->add('H1')->set($_GET['exam_id']);
   //===============================class dropdown========================================================     
        $f=$this->add('Form');
        $drp_class=$f->addField('dropdown','class');
        $class_id=$this->api->db->dsql()->expr('SELECT CONCAT( class_master.`name`, " ", class_master.section ) AS `name`, class_master.id FROM class_master, exam_map WHERE class_master.id = exam_map.class_id AND exam_map.exam_id = '.$_GET['exam_id'])->getAll();
       $cls=array();
        foreach($class_id as $a)
        {
            
            $cls+= array($a['id'] => $a['name']);
        }
        
         $drp_class->setValueList($cls);
         $f->getElement('class')->setAttr('class','hindi');  
//============================================================================================
          $f->addSubmit('search');
          
        
         if($f->isSubmitted())
           {
           
           $exammap=$this->api->db->dsql()->expr("select exam_map.id from exam_map where class_id like '%".$f->get('class')."%' and exam_id=".$_GET['exam_id'])->getOne();
    
           $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./map'),'class_id'=>$f->get('class'),'exammap_id'=>$exammap))->execute();
  

       }
       $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./map'))->set('Loading..');
    }
    function page_map()
    {
        $this->api->stickyGET('exammap_id');
        $this->api->stickyGET('class_id');
        
        if($_GET['exammap_id'])
        {
            
        
        $g=$this->add('Grid');
        $sub=$this->api->db->dsql()->expr("select subject_master.`name`,subject_master.id from subject_master,subject_class_map where subject_master.id=subject_class_map.subject_id and subject_class_map.class_id like '%".$_GET['class_id']."%'");
          $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        $g->setSource($sub);
        $cl=$this->add('Model_ExamMap')->load($_GET['exammap_id']);
        
        $form=$this->add('Form');
       $sel=$form->addField('line','sel');
        $sel->js(true)->closest('.atk-form-row')->hide();
        
        $map=$cl->ref('ExamSubMap');
  // fetches IDs
        $sel->set(json_encode($map->dsql()->del('field')->field('subject_id')->where('exammap_id',$_GET['exammap_id'])->execute()->stmt->fetchAll(PDO::FETCH_COLUMN)));
       
        $g->addSelectable($sel);

        $form->addSubmit('Save');
        if($form->isSubmitted()){
            $this->api->db->beginTransaction();

            // delete old mappings
            $map->deleteAll();
            $cl->setSub(json_decode($form->get('sel')));

            $this->api->db->commit();
            $this->js()->univ()->closeDialog()->successMessage('Mapping saved')->execute();
        }
    
        }
        else
            $this->add('H5')->set('Select Class');
           
         
         
         }
    
}