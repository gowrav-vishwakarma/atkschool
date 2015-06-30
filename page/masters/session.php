<?php

class page_masters_session extends Page{

	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');

		$grid=$this->add('Grid');
		if($_GET['mark_current']){
			$m=$this->add('Model_Session');
			$m->load($_GET['mark_current']);
			if(!$m['iscurrent']) {	
				$m->markCurrent();
				$grid->js(null,$grid->js()->reload())->univ()->successMessage("Session Changed")->execute();
			}
			else
				$grid->js()->univ()->errorMessage("This is Already Current Session")->execute();
			$grid->js()->reload()->execute();
		}

		$grid->setModel('Session',array('name','iscurrent','start_date','end_date'));
		
		$grid->addColumn('Button','mark_current');

		$btn=$grid->addButton('Create New Session');
		if($btn->isClicked()){
			$this->js()->univ()->frameURL('New Session Create',$this->api->url('session_createnew'))->execute();
		}

		// print_r($acl->getPermissions());

	}	

}