<?php

class Model_ExamSubMap extends Model_Table
{
    var $table='examsub_map';
    function init()
    {
        parent::init();
      $this->hasOne('Subject','subject_id');
		$this->hasOne('ExamMap','exammap_id');
                $this->hasOne('Session_Current','session_id');
                $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id')->getOne());        
        
    }
    
}
