<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Hostel extends Model_Table
{
    
    var $table='hostel_master';
    function init()
    {
        parent::init();
        $this->addField('building_name');
        $this->addExpression('Rooms')->set($this->add('Model_Hostel_Rooms')->dsql()->field('count(*)')->where('hostel_id',$this->getField('id')));
        $this->addExpression('capacity')->set(
                $this->add('Model_Hostel_Rooms')->dsql()->field('sum(capacity)')->where('hostel_id',$this->getField('id')));
        
        $this->addExpression('alloted')->set($this->add('Model_Hostel_Allotment')->dsql()->field('count(room_id)')
                ->where('room_id in',
                        $this->add('Model_Hostel_Rooms')->dsql()
                        ->field('rooms.id')
                        ->where('hostel_id',$this->getField('id'))));
      
           $this->addExpression("vacant")->set('id')->type('diff');
    }        
  
}
?>
