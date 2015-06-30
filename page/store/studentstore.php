<?php
class page_store_studentstore extends Page{
	function init(){
		parent::init();
    $acl=$this->add('xavoc_acl/Acl');	
    $this->api->stickyGET('class_id');  
    $this->api->stickyGET('cat');	

		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setEmptyText('----')->setNotNull()->setAttr('class','hindi');
    $cm=$this->add('Model_Class');
		$class_field->setModel($cm);
		$categary=$form->addField('dropdown','category')->setEmptyText('----')->setNotNull();
        $categary->setValueList(array('1'=>'Scholared','2'=>'Private'));


        $str=$form->addField('line','no','Starting Store No')->validateNotNull();
        $str->js(true)->univ()->numericField()->disableEnter();
        $form->addSubmit('Allot');
      $crud=$this->add('CRUD',array('allow_add'=>false,'allow_del'=>false,'allow_edit'=>false));
       

       $m=$this->add('Model_Students_Current');
       $m->_dsql()->order('fname','asc');
       if($_GET['class_id'] and $_GET['cat']){
        $m->addCondition('class_id',$_GET['class_id']);
        $m->addCondition('isScholared',($_GET['cat']==2? 0: 1));
        $m->_dsql()->order('id','asc');
       }else{
          if(!$crud->form)  $m->addCondition('class_id',-3);

       }
     // $m->debug();
      $crud->setModel($m,array('fname','name','store_no','class'));

      if($crud->grid){
        $crud->grid->addFormatter('class','hindi');
        $crud->grid->addFormatter('store_no','grid/inline');
      	  $class_field->js("change",$crud->grid->js()->reload(array('class_id'=>$class_field->js()->val(),'cat'=>$categary->js()->val())));
       		$categary->js("change",$crud->grid->js()->reload(array('cat'=>$categary->js()->val(),'class_id'=>$class_field->js()->val())));
      }else{

      }

      if($form->isSubmitted()){

        $s=$this->add('Model_Student');
        $s->addCondition('class_id',$form->get('class'));
        $s->addCondition('isScholared',$form->get('category'));
        $s->_dsql()->order('id','asc');
        // $s->debug();
        $sn=$form->get('no');
        foreach($s as $junk){
          $s['store_no']=$sn++;
          $s->save();
        }
        $crud->grid->js()->reload(array("class"=>$form->get('class'),
                                       "cat"=>$form->get('category')
                                                   ))->execute();
         
      }

	}
}