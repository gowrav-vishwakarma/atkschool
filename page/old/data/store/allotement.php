<?php

class page_data_store_allotement extends Page
{

     function init() 
    {
        parent::init();
        $f=$this->add('Form',null,null,array('form_empty'));
        
                    
        $class=$f->addField('dropdown','class')->setEmptyText('p;u d{kk');
        $class->setModel('Class');
        $class->setAttr('class','hindi');
      
        $categary=$f->addField('dropdown','category');
        $categary->setValueList(array('1'=>'Scholared','0'=>'Private'));
        
        $str=$f->addField('line','no','Starting Store No')->validateNotNull();
        $str->js(true)->univ()->numericField()->disableEnter();
        $f->addSubmit('allot');
       
        
        
        $m=$this->add('Model_Store_Allotement');
        
       
       $crud=$this->add('CRUD',array('allow_add'=>false,'allow_del'=>false));
       $crud->setModel($m,array('store_no'),array('sn','scholar','store_no','class','isScholared'));
       if($crud->grid)
       {
        
  
       $crud->grid->addColumn('template','scholar')->setTemplate('<div class="hindi"><?$scholar?></div>');
        $crud->grid->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
       
       $class->js("change",$crud->grid->js()->reload(array('class_id'=>$class->js()->val(),'cat'=>$categary->js()->val())));
       $categary->js("change",$crud->grid->js()->reload(array('cat'=>$categary->js()->val(),'class_id'=>$class->js()->val())));
       }
       if($_GET['class_id'])
        {
         
          
         
             $crud->getModel()->addCondition('class_id',$_GET['class_id']);
                                    
             $crud->getModel()->addCondition('isScholared',$_GET['cat']);
             
        }
           
       
        
        
        
        if($f->isSubmitted())
        {
            
           if($f->get('class'))
           {
//            $model=$this->add('Model_Student');
//            $model->addCondition('class_id',$f->get('class'));
//            $model->addCondition('isScholared',$f->get('category'));
               $q = $this->api->db->dsql()->expr("SELECT student.id, scholars_master.hname FROM student, scholars_master WHERE student.class_id = ".$f->get('class')." AND student.isScholared = ".$f->get('category')." AND session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE ) AND scholars_master.id = student.scholar_id and student.ishostler=true ORDER BY scholars_master.fname");
             $store_no=$f->get('no');
            foreach($q as $a)
                 {
                   
                  $this->api->db->dsql()->table('student')->set('store_no',$store_no)->where('id',$a['id'])->do_update();               
                     $store_no++;
                 }
                 
                 $f->js(null,$crud->grid->js()->reload(array('class'=>$f->get('class'))))->univ()->successMessage('alloted')->execute();
         
               
                 
           }
           else
           {
               $this->js()->univ()->alert('Select class First')->execute();
           }
                
     }
}

}