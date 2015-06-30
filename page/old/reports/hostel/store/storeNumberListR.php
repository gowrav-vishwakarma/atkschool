<?php

class page_reports_hostel_store_storeNumberListR extends Page
{
     
    function init()
    {
        parent::init();
    
      $this->api->stickyGET('class');
      $this->api->stickyGET('category');
   
        $m=$this->add('Model_Store_Allotement');
        $m->dsql()->order('store_no');
        $crud=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false,'allow_add'=>false));
               $crud->setModel($m,array('store_no','scholar','father_name','class'));
  
 
        if($_GET['class'] )
        {
          $m->addCondition('class_id',$_GET['class']);
          
        }
        
        if($_GET['category']!=null)
        {
            $m->addCondition('isScholared',$_GET['category']);
        }
if($crud->grid)
{
  $crud->grid->addColumn('template','scholar')->setTemplate('<div class="hindi"><?$scholar?></div>');
  $crud->grid->addColumn('template','father_name')->setTemplate('<div class="hindi"><?$father_name?></div>');
  $crud->grid->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
   
  
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
