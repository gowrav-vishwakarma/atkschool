<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Class_Fee_Mapping extends Model_Table{
    var $table="fee_class_mapping";
    
    function init($all=false){
        parent::init();
//        $this->hasOne('Fee','fee_id');
//        $this->hasOne('Class','class_id');
        $this->addField('fee_id');
        $this->addField('class_id');
        $this->hasOne('Session','session_id');
        if(!$all) $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id'));
            
    }
    
}