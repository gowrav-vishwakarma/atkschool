<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Hostel_Treatment extends Model_Table
{
    
    var $table='treatment';
    function init()
    {
        parent::init();
        $this->hasOne('Hostel_Disease','disease_id');
        $this->addField('name')->caption('treatment');
        $this->addField('treatment_date')->type('datetime')->defaultValue(date('Y-m-d H:i:s'));
        
        
        
        
     
        
        
                
    }
}
?>
