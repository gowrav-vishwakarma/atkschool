<?php

class page_staff_movement extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$filter_field=$form->addField('dropdown','filter_duty')->setValueList(array('h'=>'Hostel','s'=>'School','0'=>'All'))->set('0');
		$staff_field=$form->addField('dropdown','staff')->setEmptyText('---')->setNotNull()->setAttr('class','hindi');
		$grid=$form->add('Grid');
		$form->addButton('Mark In')->js('click',$form->js()->atk4_form('submitForm','mark_in'));
		$form->addButton('Mark Out')->js('click',$form->js()->atk4_form('submitForm','mark_out'));

		$sm= $this->add('Model_Staff');
		$sm4grid= $this->add('Model_Staff');
		if($_GET['filter_duty']){
			$sm->addCondition('ofhostel','like',($_GET['filter_duty']=='h')? '1':'0');
		}

		$staff_field->setModel($sm);
		$filter_field->js('change',$form->js()->atk4_form('reloadField','staff',array($this->api->getDestinationURL(), 'filter_duty'=>$filter_field->js()->val())));
		$staff_field->js('change',$grid->js()->reload(array($this->api->getDestinationURL(), 'filter_staff'=>$staff_field->js()->val())));

		if($_GET['filter_staff']){
			$sm4grid->addCondition('id',$_GET['filter_staff']);
		}else{
			$sm4grid->addCondition('id',-1);
		}

		$grid->setModel($sm4grid,array('hname','designation','contact','attendance_status','image_url'));

		if($form->isSubmitted()){
			$staff = $this->add('Model_Staff');
			$staff->load($form->get('staff'));
			$m=$staff->ref('Staff_Movement');

			if($form->isClicked('mark_in')){
				if($staff['attendance_status'] == 'inward') $form->displayError('staff','Already In');
				$m['action']="inward";
			}
			if($form->isClicked('mark_out')){
				if($staff['attendance_status'] == 'outward') $form->displayError('staff','Already Out');
				$m['action']="outward";
			}
			$m->save();
			$form->js(null,$form->js()->univ()->successMessage('Movement Recorded'))->reload()->execute();
		}
	}
}