<?php
class page_data_store_billInward extends Page
{
    
    function initMainPage()
    {
      parent::init();
      $this->api->stickyGET('party_id');
        $crud = $this->add('CRUD');
        $r=$this->add('Model_Store_Bill');
        $r->addCondition('party_id',$_GET['party_id']);
        $r->set('party_id',$_GET['party_id']);
        $r->getElement('party_id')->system(true);
        
        $crud->setModel($r);//,null,array('Bill Number','bill_date','item_date','paid'));
        
        
        if ($crud->grid) {
          
          $btn_add = $crud->grid->addColumn('Button', 'btn', 'Add Inward');
          $btn_edit=$crud->grid->addColumn('expander','ed','details');
         
        }
        if ($_GET['btn']) {
          $this->js('click')->univ()->frameURL('Item Inward', array($this->api->url('./add'), 'bill_id' => $_GET['btn']))->execute();
        }
        
        }
        
        function page_add() {
        $this->api->stickyGET('bill_id');
               
         $m= $this->setModel('Store_Inward');         
         $m->set('bill_id',$_GET['bill_id']);        
         $m->getElement('bill_id')->system(true);
         
         $f=$this->add('InfiniteAddForm');
         $f->setModel($m);
         $f->form->getElement('item_id')->setAttr('class','hindi')->setEmptyText('p;u');
        }
        
        function page_ed()
        {
        $this->api->stickyGET('id');
        
        $p= $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')->addStyle('background','#ddd');
        try
        {
           
        $total=$this->api->db->dsql()->expr("select sum(item_inward.rate* item_inward.quantity) from item_inward where bill_id=".$_GET['id'])->getOne();
        $p->add('H1')->set("Total : ".$total);
        $crud = $p->add('CRUD',array('allow_add'=>false));//->addStyle('background','#ddd');
        
        $m=$this->add('Model_Store_Inward');
        $m->addCondition('bill_id',$_GET['id']);
        $crud->setModel($m,array('item','quantity','rate'),array('item','quantity','rate','amount'));
        if($crud->grid)
        {
        $crud->grid->addColumn('template','item')->setTemplate('<div class="hindi"><?$item?></div>');
                }
        }
        catch (Exception $e) 
        {
                $this->js(null,$p->js()->univ()->alert($e->getMessage()))->execute();
        }
        
        
        }
    }