<?php

class Model_Hostel_Allotment extends Model_Table
{
    var $table = 'hostel_allotement';
    
    function init()
    {
        parent::init();
        
//       $s= $this->join('student.id','student_id');
//       $s->hasOne('Scholar','scholar_id');
//       $s->hasOne('Class','class_id');

        $this->addField('student_id');
        $this->addField('room_id');
        $this->addField('session_id');
        $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id'));
        
    }
    
    function getroomcount($room)
    {
        $ds= $this->add('Model_Session_Current')->dsql()->field('id');
          $session=$ds->do_getOne();
          $dq=$this->api->db->dsql()->table('hostel_allotement')->field('count(*)');
          $dq->where('room_id ='.$room.' and session_id = '.$session);
           
          $count= $dq->do_getOne();
          return $count;
    }
    
    function getcapacity($room)
    {
        $bq=$this->api->db->dsql()->table('rooms')->field('capacity');
          $bq->where('id ='.$room);
          $capacity = $bq->do_getOne();
          return $capacity;
    }
    
    function isAlloted($student)
    {
        $ds= $this->add('Model_Session_Current')->dsql()->field('id');
          $session=$ds->do_getOne();
        $cq=$this->api->db->dsql()->table('hostel_allotement')->field('count(*)')
               ->where('student_id='.$student.' and session_id='.$session);
          $cnt=$cq->do_getOne();
          if($cnt>0)
              return true;
          else
              return false;
    }
   
}
