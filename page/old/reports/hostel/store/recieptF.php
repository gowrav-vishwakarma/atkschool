<?php
class page_reports_hostel_store_recieptF extends Page
{
    function  init()
    {
        parent::init();
       
        $f=$this->add('Form',null,null,array('form_horizontal'));
        
                    
        $store_no=$f->addField('line','store_no')->setNotNull();
        $month=$f->addField('dropdown','month');//->setEmptyText('p;u d{kk');
        $month->setValueList(array(1=>"Jan",
                                   2=>"Feb",
            3=>"March",
            4=>"April",
            5=>"May",
            6=>"Jun",
            7=>"July",
            8=>"Aug",
            9=>"Sep",
            10=>"Oct",
            11=>"Nov",
            12=>"Dec",
            13=>"All"
           ));
        $f->addsubmit('Print');
        
           if($f->isSubmitted())
      {
       $this->js()->univ()->newWindow($this->api->url("store/reciept",array("month"=>$f->get('month'),"store_no"=>$f->get('store_no'))),null,'height=689,width=1246,menubar=1')->execute();
      }
        
    }
}