<?php

class page_master_school_class extends Page
{
    function init()
    {
        parent::init();
   
        $t=$this->add('Tabs');
        $classMaster=$this->api->auth->model['m_s_c_cmaster'];
        $subjectMaster=$this->api->auth->model['m_s_c_smaster'];
        if($classMaster==1)
           $class_master=$t->addTabURL('master_school_class_classmaster','Class Master');
        if($subjectMaster==1)
           $subject_master=$t->addTabURL('master_school_class_subjectmaster','Subject Master');
        $subject_master=$t->addTabURL('master_school_class_exammaster','Exam Master');
       // $subject_class_map=$t->addTabURL('master_class_subclassmap','Subject Class Map');

 
    }
    
}

