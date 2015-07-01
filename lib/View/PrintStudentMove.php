<?php
class View_PrintStudentMove extends View{
	public $inward;
	public $outward;
	public $enquiry;

	function init(){
		parent::init();
		$hosteler_id=$_GET['hosteler_id'];
		$gaurdian=$_GET['gaurdian'];
		$purpose=$_GET['purpose'];
		$building=$_GET['building'];
		$room_no=$_GET['room_no'];
		$remark=$_GET['remark'];
		$date=$_GET['date'];

		// echo "string".$hosteler_id. $gaurdian. $purpose . $building. $room_no . $remark.$date;
		
		$hostler_model=$this->add('Model_Hosteler');
		$scholar_j = $hostler_model->LeftJoin('scholars_master','scholar_id');
		$scholar_j->addField('hname');
		$hostler_model->load($hosteler_id);

		if($_GET['purpose']=='inward'){
			$this->template->trySet('header',"गेट पास प्रवेष हेतु");
			$this->template->trySet('gaurdian_heading',"लाने वाले का नाम");
		}
		if($_GET['purpose']=='outward'){
			$this->template->trySet('header',"गेट पास बाहर जाने हेतु");
			$this->template->trySet('gaurdian_heading',"ले जाने वाले का नाम");
		}
		if($_GET['purpose']=='enquiry'){
			$this->template->trySet('header',"गेट पास मिलने हेतु");
			$this->template->trySet('gaurdian_heading',"मिलने वाले का नाम");
		}

		$guardians_ids=explode(",",$gaurdian); 
		$guardian_names=array();

		foreach ($guardians_ids as $guardiagn_id){
			if($guardiagn_id != "" or $guardiagn_id)
			$guardian_names[] = $this->add('Model_Scholars_GuardianAll')->load($guardiagn_id)->get('name');
		} 
		

		$this->template->trySet('name',$hostler_model['hname']);
		$this->template->trySet('father_name',$hostler_model['father_name']);
		$this->template->trySetHTML('gaurdian_name',implode('] <br/>', $guardian_names));
		$this->template->trySet('building',$building);
		$this->template->trySet('room_no',$room_no);
		$this->template->trySet('date',$date);
		$this->template->trySet('discription',$remark);
		
	}

	function defaultTemplate(){
		return array('view/studentmovment/studentmovement');
	}
}
