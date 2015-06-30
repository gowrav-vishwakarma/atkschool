<?php

class Model_StudentsAndHostelers extends Model_Students_Current{

	function init(){
		parent::init();
		$this->addCondition('ishostler',true);
		$raj=$this->LeftJoin('hostel_allotement.student_id','id');
		$rj=$raj->leftJoin('rooms.id','room_id');
		$bj=$rj->leftJoin('hostel_master.id','hostel_id');
		$rj->addField('room_no');
		$bj->addField('building_name');

		$this->addField('is_present')->type('boolean')->defaultValue(false);

		$this->hasMany('Item_Issue','student_id');

		$this->addExpression('attendance_status')->set(function ($m,$q){
			return $m->refSQL('Students_Movement')->fieldQuery('purpose')->limit(1)->order('id','desc')->where('purpose','in',array('inward','outward'));
		})->display('attendance');
	}
}