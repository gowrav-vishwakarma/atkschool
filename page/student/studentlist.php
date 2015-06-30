<?php 
class page_student_studentlist_removed extends Page{
	function init(){
		parent::init();
        $acl=$this->add('xavoc_acl/Acl');
        $this->api->stickyGET('class');
        $this->api->stickyGET('sex');
        $this->api->stickyGET('category');
        $this->api->stickyGET('hostel');
        $this->api->stickyGET('scholar');
        $this->api->stickyGET('bpl');
        $this->api->stickyGET('to_age');
        $this->api->stickyGET('from_age');

        $grid=$this->add('Grid');


         $m=$this->add('Model_Scholars_Current');
        
	
        if($_GET["class"]){
        	$m->addCondition('class_id',$_GET['class']);
        }
        if($_GET["sex"]!="-1"){
        	 $m->addCondition('sex',$_GET['sex']);
        }
         if($_GET['category']!="-1")
        {
            if($_GET['category']=='tadst')
            {
                $m->addCondition('category',array('ST','TAD'));
                //$m->addCondition('category','TAD');
            } 
            else
            $m->addCondition('category',$_GET['category']);
        }

        if($_GET['hostel']!="-1")
        {
          $m->addCondition('ishostler',$_GET['hostel']);   
        }
        if($_GET['scholar']!="-1")
        {
            $m->addCondition('isScholared',$_GET['scholar']);
        }
        if($_GET['bpl']!="-1")
        {
            $m->addCondition('bpl',$_GET['bpl']);
        }

        if($_GET['to_age'])
        	$m->addCondition('age','<=',$_GET['to_age']);
        if($_GET['from_age'])
        	$m->addCondition('age','>=',$_GET['from_age']);
        // $m->debug();

        $m->_dsql()->del('order')->order('roll_no');
        $m->debug();
        $grid->setModel($m,null,
                array('sn','scholar_no','name','father_name','roll_no', 'dob','contact','p_address','sex','category'));
		
		// $grid->add('misc/Export');
	}
}