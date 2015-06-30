<?php
class page_masters_subject extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add('CRUD');
		$crud->setModel('Subject');
	}
}