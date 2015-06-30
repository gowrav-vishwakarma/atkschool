<?php


class page_user extends Page
{
    
    function init()
    {
        parent::init();
         $tabs=$this->add('Tabs'); 
         $create=$this->api->auth->model['u_create'];
         
         if($create==1)
             $tabs->addTabURL('user_create',"Create New User");
          $tabs->addTabURL('user_changepswd',"Change Passward");
    }
}
