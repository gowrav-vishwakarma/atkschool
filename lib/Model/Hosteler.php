<?php

class Model_Hosteler extends Model_Student{

	function init(){
		parent::init();
		$this->addCondition('ishostler',true);
		$raj=$this->join('hostel_allotement.student_id','id');
		$rj=$raj->join('rooms.id','room_id');
		$bj=$rj->join('hostel_master.id','hostel_id');
		$rj->addField('room_no');
		$bj->addField('building_name');

		$this->addField('is_present')->type('boolean')->defaultValue(false);

		$this->hasMany('Item_Issue','student_id');
		$this->hasMany('Students_Disease','student_id');

		// $this->addExpression('father_name')->set(function($m,$q){
		// 	return $m->refSQL('scholar_id')->fieldQuery('father_name');
		// });


		$this->addExpression('attendance_status')->set(function ($m,$q){
			return $m->refSQL('Students_Movement')->fieldQuery('purpose')->limit(1)->order('date','desc')->where('purpose','in',array('inward','outward'));
		})->display('attendance');

		$this->addExpression('image_url')->set(function($m,$q){
			return $m->refSQL('scholar_id')->fieldQuery('image_url');
		})->display('picture');
	}
}