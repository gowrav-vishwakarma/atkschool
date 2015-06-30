<?php
class page_store_totalreciept extends Page{
  function init(){
    parent::init();
    $acl=$this->add('xavoc_acl/Acl');
      $this->api->stickyGET('store_no');

      $this->add('View_TotalReceipt',array('store_no'=>$_GET['store_no'],'month'=>$_GET['month']));

  }
}