<?php

class Model_Store_Issue extends Model_Table
{
var $table='item_issue';
    
    function init()
    {
        parent::init();
        $this->hasOne('Student_Current','student_id');       
        $this->hasOne('Store_Item','item_id');
        $this->addField('quantity');
        $this->hasOne('Session_Current','session_id');

        $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id'));

        $this->addField('date')->type('date')->defaultValue(date('d-m-Y'));
    }
}