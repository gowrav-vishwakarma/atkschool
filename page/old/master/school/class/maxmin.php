<?php

class page_master_school_class_maxmin extends Page
{
    function initMainPage()
    {
        parent::init();
        $f=$this->add('Form');
        //=================================exam dropdown===========================================
        $drp_exam=$f->addField('dropdown','exam');
        $f->getElement('exam')->setAttr('class','hindi'); 
        $exam=$this->add('Model_Exam');
        $d_exam=array("%"=>"p;u");
        foreach($exam as $a)
        {
            $d_exam+=array($a['id']=>$a["name"]);
        }
        $drp_exam->setValueList($d_exam);
        //================================class==================================================
        $drp_class=$f->addField('dropdown','class');
        $f->getElement('class')->setAttr('class','hindi'); 
        $class=$this->add('Model_Class');
//        $d_class=array("%"=>"p;u");
//        foreach($class as $a)
//        {
//            $d_class+=array($a['id']=>$a["name"]);
//        }
        $drp_class->setModel($class,array('name'));
        //===============================filling class===================================================
        $drp_exam->js('change', $f->js()->atk4_form('reloadField', 'class', array($this->api->getDestinationURL(), 'exam_idx' => $drp_exam->js()->val())));
        if ($_GET['exam_idx']) {
            $class_id=$this->api->db->dsql()->table('exam_map')->field('class_id');
             $class_id->where('exam_id',$_GET['exam_idx'])->get();
            
             $drp_class->dq
                    ->where('id in ',$class_id);
        }
        //======================================================================================
        $f->addSubmit('search');
        if ($f->isSubmitted()) {
//            $this->js()->univ()->successMessage($f->get('student'))->execute();
            $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./search'),'class_id'=>$f->get('class'),'exam_id'=>$f->get('exam')))->execute(); 
             }
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./search'))->set('Loading..');
    
        
    }
    
}
