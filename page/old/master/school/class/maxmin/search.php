<?php

class page_master_school_class_maxmin_search extends Page
{
    function initMainPage()
    {
        parent::init();
        $this->api->stickyGET('class_id');
        $this->api->stickyGET('exam_id');
        if($_GET['class_id'])
        {
            $subject=$this->api->db->dsql()->expr("SELECT subject_master.`name` AS `subject`, examsub_map.id as id,examsub_map.max_marks AS max, examsub_map.min_marks AS min FROM subject_master, examsub_map, exam_map WHERE examsub_map.exammap_id = exam_map.id AND exam_map.exam_id like '%".$_GET['exam_id']."%' AND exam_map.class_id =".$_GET['class_id']." AND subject_master.id = examsub_map.subject_id");
             $g=$this->add('Grid');
             $g->setSource($subject);
             $g->addColumn('template','subject')->setTemplate('<div class="hindi"><?$subject?></div>');;
             $g->addColumn('text','max');
             $g->addColumn('text','min');
             $g->addColumn('expander','add');
        }
        else
            $this->add('H3')->set('Select ExamType and Class');
        
    }
    function page_add()
    {
        $this->api->stickyGET('id');
     
        $f=$this->add('Form');
        $max=$f->addField('line','max','Max Marks');
        $min=$f->addField('line','min','Min Marks');
        $f->addSubmit();
        if($f->isSubmitted())
        {
            $max = $f->get('max'); $min = $f->get('min');
            if($f->get('max')=="")
                $max = "' '";
            if($f->get('min')=="")
                $min = "' '";
            $this->api->db->dsql()->expr("update examsub_map set max_marks=".$max.", min_marks=".$min." where examsub_map.id=".$_GET['id'])->execute();
            $this->js()->univ()->closeExpander()->successMessage('Record Saved')->execute();
            
        }
    }
}
