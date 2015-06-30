<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Student_Current extends Model_Student {
    function init(){
        parent::init();
        $this->hasOne('Session_Current','session_id');
        $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id'));
        $this->join('scholars_master','scholar_id');
        $this->_dsql()->order(array('class_id','fname')); 
        
    }
}
