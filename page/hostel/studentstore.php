<?php
class page_hostel_studentstore extends Page{
	function init(){
		parent::init();
    $acl=$this->add('xavoc_acl/Acl');		
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_field=$form->addField('dropdown','class')->setEmptyText('----')->setNotNull();
		$class_field->setModel('Class');

		$categary=$form->addField('dropdown','category')->setEmptyText('----')->setNotNull();
        $categary->setValueList(array('1'=>'Scholared','2'=>'Private'));


        $str=$form->addField('line','no','Starting Store No')->validateNotNull();
        $str->js(true)->univ()->numericField()->disableEnter();
        $form->addSubmit('Allot');
       

       $m=$this->add('Model_Students_Current');
       if($_GET['class_id'] and $_GET['cat']){
       	$m->addCondition('class_id',$_GET['class_id']);
       	$m->addCondition('isScholared',($_GET['cat']==2? 0: 1));
        $m->_dsql()->order('id','asc');
       }else{
       	$m->addCondition('class_id',-3);

       }
      $crud=$this->add('CRUD',array('allow_add'=>false,'allow_del'=>false));
      $crud->setModel($m,array('name','store_no','class','isScholared'));

      if($crud->grid){
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