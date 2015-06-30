<?php

class View_MS_Attendance extends View {
	function init(){
		parent::init();
	}
	function setModel($m){
		$upsthiti=0;
		$total_meeting=0;
		foreach($m as $junk){
			$this->template->trySet("total_".$m['month'], $m['total_attendance']);
			$this->template->trySet("att_".$m['month'], $m['present']);
			$upsthiti += $m['total_attendance'];
			$total_meeting += $m['present'];
			// echo $month;
		}
			$this->template->trySet("total_meeting", $total_meeting);
			$this->template->trySet("total_att", $upsthiti);
		parent::setModel($m);
	}

	function defaultTemplate(){
		return array('view/marksheet/attendance');
	}
}