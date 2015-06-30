<?php

class page_reports_school_studentlistR extends Page
{
     
    function init()
    {
        parent::init();
        
        $this->api->stickyGET('class');
        $this->api->stickyGET('sex');
        $this->api->stickyGET('category');
        $this->api->stickyGET('hostel');
        $this->api->stickyGET('scholar');
        $this->api->stickyGET('bpl');
        $this->api->stickyGET('to_age');
        $this->api->stickyGET('from_age');

        if($_GET['from_age']==null)
            $from=0;
        else 
            $from=$_GET['from_age'];
             
        if($_GET['to_age']==null)
            $to=50;
        else {
            $to=$_GET['to_age'];
             }
        
        $f=$this->add('Form',null,null,array('form_empty'));
        if($_GET['class'])
            $f->addField('line','class')->disable()->set($this->api->db->dsql()->expr("select concat(`name`,'-',section) from class_master where id=".$_GET['class'])->getOne())->setAttr('class','hindi');
        else
              $f->addField('line','class')->disable()->set(' All');
        
        if($_GET['category'])
            $f->addField('line','category')->disable()->set($_GET['category']);
        else
              $f->addField('line','category')->disable()->set(' All');
        if($_GET['bpl'])
            $this->add('H5')->set('BPL');

       $m=$this->add('Model_Scholar_Current');
       $crud=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false,'allow_add'=>false));
               $crud->setModel($m,null,
                array('sn','scholar_no','name','father_name','dob','contact','p_address','sex','category'));
 
  
 
        if($_GET['class'] )
        {
          $m->addCondition('class_id',$_GET['class']);
         
        }
        if($_GET['sex'])
        {
                     $m->addCondition('sex',$_GET['sex']);

        }
        if($_GET['category'])
        {
            if($_GET['category']=='tadst')
            {
                $m->addCondition('category',array('ST','TAD'));
                //$m->addCondition('category','TAD');
            } 
            else
            $m->addCondition('category',$_GET['category']);
        }
        if($_GET['hostel']!=null)
        {
          $m->addCondition('ishostler',$_GET['hostel']);   
        }
        if($_GET['scholar']!=null)
        {
            $m->addCondition('isScholared',$_GET['scholar']);
        }
        if($_GET['bpl']!=null)
        {
            $m->addCondition('bpl',$_GET['bpl']);
        }
        
        $m->addCondition($this->api->db->dsql()->expr('year(now())-year(dob) between '.$from.' and '.$to.''));        
if($crud->grid)
{
  $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
  $crud->grid->addColumn('template','father_name')->setTemplate('<div  class ="hindi"><?$father_name?></div>');
  $crud->grid->addColumn('template','p_address')->setTemplate('<div class="hindi"><?$p_address?></div>');
   
  
}
      
    }
    
    function render(){
        $this->api->template->del("Menu");
        $this->api->template->del("logo");
        $this->api->template->trySet("Content","")  ;
        $this->api->template->trySet("Footer","")  ;
        
        parent::render();
    }
   
    
}
