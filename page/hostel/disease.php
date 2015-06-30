<?php
class page_hostel_disease extends page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
	}
} 