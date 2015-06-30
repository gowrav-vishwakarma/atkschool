<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class myExport extends Export_Basic
{
     
    function init(){
        parent::init();
       $csv= $this->add("Export_Parser_CSV");
      $csv->button_label = "Export to Excel";  
        
       
    }
    
}
