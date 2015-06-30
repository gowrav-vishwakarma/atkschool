<?php
class Model_Students_Movement extends Model_Table{
	var $table="hosteller_outward";
	function init(){
		parent::init();

		$this->hasOne('Students_Current','student_id');
		$this->hasOne('Scholars_GuardianAll','gaurdian_id');
		$this->hasOne('Sessions_Current','session_id');
		$this->addField('date')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
		$this->addField('purpose')->enum(array('inward','outward','enquiry'))->mandatory('Purpose must be selected');
		$this->addField('remark');
		$this->addField('direction');

		$st=$this->leftJoin('student.id','student_id');
		$st->hasOne('Class','class_id');

		$this->_dsql()->order('hosteller_outward.id','desc');
		$this->addHook('afterDelete',$this);


        
	}

	function afterDelete(){
		$h=$this->add('Model_Hosteler');
		$h->load($this['student_id']);
		if($h['attendance_status']=='inward') $h['is_present']=true;
		if($h['attendance_status']=='outward') $h['is_present']=false;

		$h->save();

	}

}

