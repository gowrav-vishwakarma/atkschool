<?php

class page_data_store_issueInfinite extends Page
{

    
    function initMainPage()
    {
        parent::init();
         $this->api->stickyGET('store_no');
         $student_id=$this->api->db->dsql()->expr("select student.id from student where store_no=".$_GET['store_no'])->do_getOne();
         $m=$this->add('Model_Store_Issue');
            $m->getElement('student_id')->system(true);
            $m->getElement('date')->system(true);
            $f=$this->add('Form',null,null,array('form_horizontal'));
            $f->setModel($m);
            $drp_item=$f->getElement('item_id');
            $drp_item->setEmptyText('p;u')->setAttr('class','hindi');
            $txt_quantity=$f->getElement('quantity')->setNotNull();
            $txt_quantity->js(true)->univ()->numericField();
                        
            
            
        
    }
}    