<?php
class page_reports_school_studentlistF extends Page
{
    function  init()
    {
        parent::init();
        $form=$this->add('Form',null,null,array('form_empty'));
        
        //$form->addField('dropdown','student')->setAttr('class','hindi')->setEmptyText('p;u')->setModel('Scholar');
        $class=$form->addField('dropdown','class');
                $class->setAttr('class','hindi')->setEmptyText('p;u d{kk')->setModel('Class');
        $sex=$form->addField('dropdown','sex')->setValueList(array(''=>'Select','M'=>'Male','F'=>'Female'));
        $cat=$form->addField('dropdown','category')->setValueList(array(''=>'Select','GEN'=>'GEN','SC'=>'SC','ST'=>'ST',"TAD"=>"TAD(ST)","OBC"=>"OBC","SOBC"=>"SPECIAL OBC","MINORITY"=>"MINORITY","tadst"=>"TAD WITH ST"));
        $hostler=$form->addField('dropdown','hostel')->setValueList(array(''=>'Both',true=>'Hostler',false=>'Local'));
        $bpl=$form->addField('dropdown','BPL')->setValueList(array(''=>'Both',true=>'Yes',false=>'No'));
        $scholar=$form->addField('dropdown','scholar')->setValueList(array(''=>'Both',true=>'Scholared',false=>'Private'));
        $from_age=$form->addField('line','from_age')->js(true)->univ()->numericField();
        $to_age=$form->addField('line','to_age')->js(true)->univ()->numericField();
//        $crud=$this->add('CRUD',array('allow_del'=>false,'allow_edit'=>false,'allow_add'=>false));
//                $crud->setModel('Scholar_Current',null,
//                array('scholar_no','name','father_name','class','dob','contact','p_address','sex','category'));
      
      $form->addSubmit('Print');
      
     
      if($form->isSubmitted())
      {
       $this->js()->univ()->newWindow($this->api->url("reports/school/studentlistR",array("class"=>$form->get('class'),"sex"=>$form->get('sex'),"category"=>$form->get('category'),"hostel"=>$form->get('hostel'),"scholar"=>$form->get('scholar'),"bpl"=>$form->get('BPL'),"to_age"=>$form->get('to_age'),"from_age"=>$form->get('from_age'))),null,'height=689,width=1246,menubar=1')->execute();
      }
         
    }
   
}