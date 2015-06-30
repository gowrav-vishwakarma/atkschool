<?php

class Model_Store_Bill extends Model_Table {

    var $table = 'bill_master';

    function init() 
    {
     parent::init();
    $this->addField('name')->caption('Bill Number');
    //$this->addExprEssion('Bill Number')->set('name');
    $this->addField('party_id');
    $this->addField('bill_date')->type('date')->defaultValue(date('d-m-Y'));    
    $this->addField('item_date')->type('date')->defaultValue(date('d-m-Y'))->caption('Inward date');    
    
    $this->addField('cheque_number');
    $this->addField('cheque_date')->type('date')->defaultValue(NULL);
    $this->addField('paid')->type('boolean');
    
    }
}