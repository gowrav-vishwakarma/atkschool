<?php
class page_school_studentClassMapping extends Page {
	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$grid=$this->add('Grid');
		$c=$this->add('Model_Class');
		$grid->setModel($c,array('name','no_of_students','no_of_subjects'));
		$grid->addColumn('Expander','students');
		$grid->addColumn('Button','Add');
		if($_GET['Add']){
			$grid->js()->univ()->frameURL("Add Student to This Class ", $this->api->url('./addstudents',array('class_id'=>$_GET['Add'])))->execute();
		}
	}

	function page_students(){
		$v=$this->add('View')->addClass('atk-box ui-widget-content');
		$this->api->stickyGET('class_master_id');
		$class=$this->add('Model_Class');
		$class->load($_GET['class_master_id']);
	
		$grid = $v->add('Grid');
		$grid->addQuickSearch(array('fname'));
		$scm=$class->ref('Students_Current');
		$scm->_dsql()->del('order')->order('fname','asc');
		$grid->setModel($scm,array('fname','name','father_name','section','roll_number','ishostler','isScholared','bpl'));
		$grid->addColumn('Confirm','Remove');
		$grid->addFormatter('father_name','hindi');
		if($_GET['Remove']){
			$s=$this->add('Model_Students_Current');
			$s->load($_GET['Remove']);
			$s->delete();
			$grid->js(null,$grid->js()->reload())->univ()->successMessage('Student Removed From this Class')->execute();
		}

	}

	function page_addstudents(){
		$this->api->stickyGET('class_id');
		$grid=$this->add('Grid');
		$m=$this->add('Model_Scholar');
		$m->addCondition('active_in_session',false);
		$grid->setModel($m,array('name','scholar_no','image_url'));
		$grid->addColumn('Button','Add');
		if($_GET['Add']){
			$s=$this->add('Model_Students_Current');
			$s['scholar_id']=$_GET['Add'];
			$s['class_id']=$_GET['class_id'];
			$s->save();
			$grid->js()->univ()->closeDialog()->execute();
		}
	}
}