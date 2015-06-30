<?php

class View_MS_Result extends View {
	var $result;
	var $division;
	var $distinction;
	var $rank;
	var $grace;
	var $supplimentry;
	var $show_grade;
	var $grade;

	function init(){
		parent::init();
		$this->template->trySet('final_result',$this->result['final_result']);
		$this->template->trySet('percentage',round($this->result['percentage'],2));
		$this->template->trySet('division',$this->result['division']);
		$this->template->trySet('rank_in_class',$this->rank);
		if(!$this->show_grade) {
			$this->template->tryDel('show_grade');
		}else{
			$this->template->trySet('grade',$this->grade);
		}
		// $this->template->trySet('today_date',date('d/m/Y'));

		$dist="";
		foreach($this->distinction as $sub){
			$dist .= "<tr><td>";
			$dist .= $sub;
			$dist .= "</td></tr>";
		}
		$this->template->trySetHTML('distinction',$dist);

		// print_r($this->grace);

		$gr="";
		foreach($this->grace as $grace){
			$sub=array_keys($grace);
			$sub=$sub[0];
			$num = array_values($grace);
			$num=$num[0];
			$gr.="<tr><td>".$sub."</td><td>".$num."</td></tr>";
		}
		$this->template->trySetHTML('grace',$gr);

		$sup="";
		foreach($this->supplimentry as $sub=>$marks){
			$sup.="<tr><td>".$sub."</td></tr>";
		}
		$this->template->trySetHTML('supplimentry',$sup);

		
		
	}

	function defaultTemplate(){
		return array('view/marksheet/result');
	}
}