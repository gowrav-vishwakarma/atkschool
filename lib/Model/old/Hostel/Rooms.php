<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Hostel_Rooms extends Model_Table
{
    
    var $table='rooms';
    function init()
    {
        parent::init();
        $this->addField('room_no')->type('int');
        $this->addField('capacity')->type('int');
        $this->hasOne('Hostel','hostel_id');
        $this->addExpression('name')->set('room_no');
        $this->addExpression('alloted')->set($this->add('Model_Hostel_Allotment')
                ->dsql()->field('count(*)')->where('room_id',$this->getField('id')));
        $this->addExpression('vacant')->set('id')->type('diff');
        
                
    }
}
?>
