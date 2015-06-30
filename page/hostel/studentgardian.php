<?php

class page_hostel_studentgardian extends Page{

	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('class_id');
		
		$grid=$this->add('Grid');
		$form=$grid->add('Form',null,'grid_buttons');
		$class_field=$form->addField('dropdown','class')->setEmptyText('---')->setNotNull()->setAttr('class','hindi');
		$class_field->setModel('Class');

		$class_field->js('change',$grid->js()->reload(array('class_id'=>$class_field->js()->val())));

		$r=$this->add('Model_Scholars_Current');
        $r->addCondition('ishostler',true);
        if($_GET['class_id'])
        	$r->addCondition('class_id',$_GET['class_id']);

        	$r->_dsql()->del('order')->order('fname','asc');
        $grid->setModel($r,array('fname','hname','father_name','class'));
        $grid->addFormatter('class','hindi');
        $grid->addPaginator();
        $grid->addQuickSearch(array('fname'));

        $grid->addColumn('expander','manage','Manage');
	}

	function page_manage(){
		$v=$this->add('View');
		$v->addClass('atk-box ui-widget-content ui-corner-all')->addStyle('background','#eee');
		$this->api->stickyGET('scholars_master_id');
		$sc=$this->add('Model_Scholars_Current');
		$sc->load($_GET['scholars_master_id']);

		$crud=$v->add('CRUD');
		
		$crud->setModel($sc->ref('Scholars_GuardianAll'),array('gname','contact','relation','address','image','is_active'),array('gname','contact','relation','address','image_url','is_active'));
	}
}