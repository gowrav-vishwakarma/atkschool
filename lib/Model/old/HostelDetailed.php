<?php


class Model_HostelDetailed extends Model_Hostel{
	function init(){
		parent::init();
		$this->hasMany('Rooms','hostel_id');
	}
}