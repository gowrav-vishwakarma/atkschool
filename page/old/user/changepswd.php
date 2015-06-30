<?php

class page_user_changepswd extends Page
{
    
    function init()
    {
        parent::init();
        
   $id=$this->api->auth->model['id'];
   $name=$this->api->auth->model['username'];
   $this->add('H3')->set("USERNAME :  ".$name);
   $f=$this->add('Form');
   $old=$f->addField('password','old','Old Password');
   $f->addField('password','new','New password');
   $f->addField('password','confirm','Confirm password');
   $f->addSubmit();
   $oldpswd=$this->api->db->dsql()->expr("select password from users where id =".$id)->do_getOne();
   if($f->isSubmitted())
   {
       if($oldpswd!=$f->get('old'))
       {
           
           
           $f->displayError('old', "Old Password did not Matched" );
           
                return;
       }
       if($f->get('new')!=$f->get('confirm'))
           {
           $f->displayError('confirm', "New and Confirm Passwords do not Match" );
                return;
           }
           
           $save=$this->api->db->dsql()->expr("update users set password='".$f->get('confirm')."' where id=".$id)->execute();
           $this->js()->univ()->alert('Password changed Successfully')->execute();
   }
       
    }
}