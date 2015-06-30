<?php

class Model_Exam  extends Model_Table
{
    
    var $table='exam_master';
    function init()
    {
        parent::init();
        $this->addField('name');

        $this->hasMany("ExamMap","exam_id");
        
                
    }
    function setClass($ids)
    {
        $ss=$this->add('Model_Session_Current')->loadAny();
    	
        
        if($ids==null)
        {
            $this->api->db->dsql()->expr("DELETE from exam_map where exam_map.exam_id =" .$this->id)->execute();
            return;
            
        }
        $string="";
        foreach($ids as $id)
        {
        $string.=$id.",";    
       
        }
        $cut=substr($string,0,-1); 
        $this->api->db->dsql()->expr("DELETE from exam_map where exam_map.exam_id =" .$this->id." and exam_map.class_id not in (".$cut.")")->execute();     
           foreach($ids as $id){
    		//$res[]=array('class_id'=>$id, 'exam_id'=>$this->id, 'session_id'=>$ss->id);
              $nthng=$this->api->db->dsql()->expr("select count(exam_map.class_id) from exam_map where exam_map.exam_id=".$this->id." and class_id= ".$id)->do_getOne();        
              if($nthng)
              {
               
              }
              else
              {
                  $this->api->db->dsql()->expr("insert into exam_map (class_id,exam_id,session_id) values(".$id.",".$this->id.",".$ss->id.")" )->execute();
              }
             
                
    	}
    	//$this->ref('ExamMap')->dsql()->insertAll($res);
    }
}
?>
