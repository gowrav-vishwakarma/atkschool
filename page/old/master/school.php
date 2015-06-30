<?php

class page_master_school extends Page
{
    
    function init()
    {
        parent::init();
         $tabs=$this->add('Tabs'); 
         $session=$this->api->auth->model['m_s_session'];
         $class=$this->api->auth->model['m_s_class'];
         $fee=$this->api->auth->model['m_s_fee'];
        if($session==1)
           $tabs->addTabURL('master_school_session',"Sessions");
        if($class==1)
           $tabs->addTabURL('master_school_class','Classes');
        if($fee==1)
           $tabs->addTabURL('master_school_feemaster','Fee Master');
    }
}