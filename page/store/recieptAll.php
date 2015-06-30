<?php
class page_store_recieptAll extends Page{
  function init(){
    parent::init();
    $acl=$this->add('xavoc_acl/Acl');
      $this->api->stickyGET('month');
      $this->api->stickyGET('store_no');

      $this->add('View_ReceiptAll',array('store_no'=>$_GET['store_no'],'month'=>$_GET['month']));

  }
}