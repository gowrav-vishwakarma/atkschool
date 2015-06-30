<?php
class page_reports_hostel_store_storeNumberListF extends Page
{
    function  init()
    {
        parent::init();
       
        $f=$this->add('Form',null,null,array('form_horizontal'));
        
                    
        $class=$f->addField('dropdown','class')->setEmptyText('p;u d{kk');
        $class->setModel('Class');
        $class->setAttr('class','hindi');
      
        $category=$f->addField('dropdown','category')->setEmptyText('All' );
        $category->setValueList(array(null=>'ALL','1'=>'Scholared','0'=>'Private'));
        $f->addSubmit('Print');
        if($f->isSubmitted())
      {
       $this->js()->univ()->newWindow($this->api->url("reports/hostel/store/storeNumberListR",array("class"=>$f->get('class'),"category"=>$f->get('category'))),null,'height=689,width=1246,menubar=1')->execute();
      }
        }
}