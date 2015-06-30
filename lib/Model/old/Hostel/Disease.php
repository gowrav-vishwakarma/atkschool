<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Hostel_Disease extends Model_Table
{
    
    var $table='disease_master';
    function init()
    {
        parent::init();
        
       $this->hasOne('Scholar','scholar_id');
       $m=$this->join('student','scholar_id');
       $m->hasOne('Class','class_id');
        $this->addExpresion('SN')->set();
        $this->addField('disease');
        $this->addField('report_date')->type('date');
        $this->addField('treatment')->type('boolean');
        //$m->join('hostel_allotement','id','student_id');
        
        
        
        
     
        
        
                
    }
}
?>
