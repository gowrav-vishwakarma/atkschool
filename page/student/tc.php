<?php
class page_student_tc extends Page{
	function page_index(){
		// parent::init();

		// $scholar=$this->add('Model_Scholar');
		// $scholar->addExpression('age_years')->set("(SELECT FLOOR((admission_date - dob) / 31536000))");
		// $scholar->addExpression('age_month')->set("(SELECT MOD((admission_date - dob) / 31536000 * 12, 12))"); 
		// $scholar->tryLoadAny();

		// $this->add('View_Scholar')->setModel($scholar);


		$form=$this->add('Form',null,null,array('form_horizontal'));

		$form->addField('line','scholar_no',"Enter Scholar No");


		if($form->isSubmitted()){

			$scholar=$this->add('Model_Scholar');
			$scholar->addCondition('scholar_no',$form->get('scholar_no'));
			$scholar->tryLoadAny();
			if(!$scholar->loaded())
				$form->displayError('scholar_no','This is not a valid scholar_no');

			$form->js(null,$form->js()->reload())->univ()->newWindow($this->api->url("./tcform",array('scholar_id'=>$scholar->id)),null,'height=689,width=1246,scrollbar=1')->execute();

		}
	}


	function page_tcform(){

		$this->api->stickyGET('scholar_id');
		$scholar=$this->add('Model_Scholar');
		// $scholar->addExpression('age_years')->set("(SELECT FLOOR((admission_date - dob) / 31536000))");
		// $scholar->addExpression('age_month')->set("(SELECT MOD((admission_date - dob) / 31536000 * 12, 12))"); 
		
		$scholar->load($_GET['scholar_id']);

		$this->add('View_Scholar')->setModel($scholar);

		$this->api->template->tryDel('header');
	}
}