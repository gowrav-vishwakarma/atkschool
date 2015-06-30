<?php

class page_master extends Page
{
    function init()
    {
        parent::init();
     //  $perms=perform_my_auth('master');
       
        
      //  $this->add("H1")->set("permission is ". $pp);
        
         $tabs=$this->add('Tabs');
     
         
         $ms=$this->api->auth->model['m_school'];
         $mh=$this->api->auth->model['m_hostel'];
         if($ms==1)
         {
         $tabs->addTabURL('master_school',"School");
         }
        if($mh==1)
        {  $tabs->addTabURL('master_hostel',"Hostel");}
        $tabs->addTabURL('master_store',"Store");
         //$tabs->addTabURL('master_class','Classes');
         //$tabs->addTabURL('master_scholar',"Current Scholars");
         //$tabs->addTabURL('master_feemaster','Fee Master');
         //$tabs->addTabURL('student_fee_mapping','Student Fee Mapping');
         //$tabs->addTabURL('master_hostel','Hostel');
        // $tabs->addTabURL('master_staff','Staff');
      

}
}
?>
