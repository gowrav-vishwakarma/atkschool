<?php
class page_reports_hostel_disease_diseaseF extends Page
{
    function  init()
    {
        parent::init();
       
        $f=$this->add('Form',null,null,array('form_horizontal'));
        
                    
        $drp_class=$f->addField('dropdown','class')->setEmptyText('p;u d{kk');
        $drp_class->setModel('Class');
        $drp_class->setAttr('class','hindi');
       
        $drp_std = $f->addField('dropdown', 'scholar')->setEmptyText('p;u' );
        $r = $this->add('Model_Scholar_Current');
        
       

        $r->addCondition('ishostler', true);
        $drp_std->setModel($r, array('name'));
        $f->getElement('scholar')->setAttr('class', 'hindi');
        $drp_class->js('change', $f->js()->atk4_form('reloadField', 'scholar', array($this->api->getDestinationURL(), 'class_idx' => $drp_class->js()->val())));
        if ($_GET['class_idx']) {
            
                $drp_std->dq
                    ->where('class_id like ', '%' . $_GET['class_idx'] . '%')->order('fname');
        }
        
        $f->addSubmit('Print');
        if($f->isSubmitted())
      {
       $this->js()->univ()->newWindow($this->api->url("reports/hostel/disease/diseaseR",array("class"=>$f->get('class'),"scholar"=>$f->get('scholar'))),null,'height=689,width=1246,menubar=1')->execute();
      }
        }
}