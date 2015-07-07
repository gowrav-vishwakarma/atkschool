<?php

class page_masters extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$tab=$this->add('Tabs');
		$tab->addTabURL('masters_session','Session');
		$tab->addTabURL('masters_news','News');
		$tab->addTabURL('masters_welcome','Welcome Logo');
		$tab->addTabURL('masters_class','Class');
		$tab->addTabURL('masters_subject','Subject');
		$tab->addTabURL('masters_exam','Exam');
		$tab->addTabURL('masters_hostel','Hostel');
		$tab->addTabURL('masters_category','Category');
		$tab->addTabURL('masters_item','Item');
		$tab->addTabURL('masters_party','Party');
		$tab->addTabURL('masters_feeshead','Fees Head');
		$tab->addTabURL('masters_fee','Fees');
		$tab->addTabURL('masters_disease','Disease');
		$tab->addTabURL('masters_scholars','Scholars');
		// $tab->addTabURL('masters_division','Division');
		// $tab->addTabURL('masters_grade','Grade');
		$tab->addTabURL('masters_marksheetdesigner','Marksheet Designer');
	}
}