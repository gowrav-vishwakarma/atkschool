<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Feehead extends Model_Table {
    var $table='fee_heads';
    function init(){
        parent::init();
        $this->addField('name')->mandatory("Fee Head name is must");
        $this->hasMany('Fee', 'feehead_id');

        // $this->addHook('beforeSave',$this);
        $this->addHook('beforeDelete',$this);
    }

    // function beforeSave(){

    // }

}