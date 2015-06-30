<?php

class page_data_store_inward extends Page{
    
    function initMainPage()
    {
        parent::init();
        $crud = $this->add('CRUD', array('allow_add' => false, 'allow_edit' => false, 'allow_del' => FALSE));
        $r=$this->add('Model_Store_Party');
        
        $crud->setModel($r,array('name'));
        
        if ($crud->grid) {
          
          $btn_add = $crud->grid->addColumn('Button', 'btn', 'Add Inward');
        
         
          $crud->grid->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        }
        if ($_GET['btn']) {
          $this->js('click')->univ()->frameURL('Inward', array($this->api->url('data/store/billInward'), 'party_id' => $_GET['btn']))->execute();
        }
        
        }
        
        
        
        
}