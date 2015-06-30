<?php

class Model_Scholar_Current_Guardian extends Model_Table
{
    
   var $table='scholar_guardian';
    function  init()
    {
       
        parent::init();
        $this->addField('gname');
        $this->addField('image');
        $this->addField('relation');
        //$this->hasOne('Scholar_Current','scholars_master.id','scholar_guardian.scholar_id');
    }
    
}