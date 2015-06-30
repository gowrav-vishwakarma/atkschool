<?php

class Model_Hostel_HostelStudents extends Model_Table
{
    var $table='hostel_allotement';
    function  init()
    {
        
        parent::init();
           
          $id=$this->join('student','student_id');
          $sci=$id->join('scholars_master','scholar_id');
          $sci->addField('hname')->caption('Student');
          $this->addExpression('Student_name')->set('hname'); 
          $cls=$id->join('class_master','class_id');
          $cls->addField('name')->caption('class');
          $this->hasOne('Hostel_Rooms','room_id');
          $sc=$this->join('rooms','room_id');
          $hm= $sc->join('hostel_master','hostel_id');
 
          $hm->addField('building_name');
    }
}
