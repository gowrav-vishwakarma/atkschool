<?php

class Model_ExamMap extends Model_Table
{
    var $table='exam_map';
    function init()
    {
        parent::init();
        $this->hasOne('Class','class_id');
		$this->hasOne('Exam','exam_id');
                $this->hasOne('Session_Current','session_id');
                $this->hasMany("ExamSubMap","exammap_id");
                $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id')->getOne());
    }
    
     function setSub($ids)
    {
        $ss=$this->add('Model_Session_Current')->loadAny();
    	foreach($ids as $id){
    		$res[]=array('subject_id'=>$id, 'exammap_id'=>$this->id, 'session_id'=>$ss->id);
    	}
    	$this->ref('ExamSubMap')->dsql()->insertAll($res);
    }
}