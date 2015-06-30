<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Fee extends Model_Table {

    var $table = 'fee';

    function init() {
        parent::init();
        $this->addField('name')->mandatory('Fee Head Should have a name')->caption('Fee Type');
        $this->addField('amount')->type("int")->mandatory("Fee Amount should be entered");
        $this->addField('scholaredamount')->type('int')->caption('Scholared Amount');
        $this->addField('isOptional')->type('boolean')->caption('IsOptional');
        $this->hasOne('Feehead', 'feehead_id');
        $this->hasMany('Class_Fee_Mapping','fee_id');
    }

    
    

}