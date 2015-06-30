<?php

class page_master_school_feemaster extends Page
{
    function init()
    {
        parent::init();
        $tabs=$this->add('Tabs');
        $feehead=$this->api->auth->model['m_s_f_fhead'];
        $fee=$this->api->auth->model['m_s_f_fee'];
        if($feehead==1)
              $tabs->addTabURL('../fee_head',"Fee Head");
        if($fee==1)          
        $tabs->addTabURL('master_school_fee',"Fee");
      
  
        $tabs->addTabURL('master_studentex',"StudentEx");
//           $tabs->addTabURL('master_student',"Student");
//        
    }
    
}
?>
