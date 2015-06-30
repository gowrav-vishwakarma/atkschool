<?php

class page_data_staff_report extends Page
{
    function initMainPage()
    {
        parent::init();
        
        $f = $this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
        $drp_duty = $f->addField('dropdown','duty');
        $drp_duty->setValueList(array(""=>"Select Duty","1"=>"Hostel","not 1"=>"School"));
 //========================================================================================================================
        
        $drp_staff = $f->addField('dropdown', 'staff');

        $r = $this->add('Model_Staff');

        $drp_staff->setModel($r, array('hname'));
        $f->getElement('staff')->setAttr('class', 'hindi');
        $drp_duty->js('change', $f->js()->atk4_form('reloadField', 'staff', array($this->api->getDestinationURL(), 'ishostel' => $drp_duty->js()->val())));
        if ($_GET['ishostel']) {
            $drp_staff->dq
                    ->where('ofhostel',$_GET['ishostel']);
        }
        
       
        $f->addSubmit('List');
        if ($f->isSubmitted()) {
               $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./list'),'staff_id'=>$f->get('staff')))->execute(); 
        }
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./list'))->set('Loading..');
        
    }
    
    function page_list() {

        $this->api->stickyGET('staff_id');

        
            $br = $this->api->db->dsql()
                  ->expr("SELECT staff_outward.id as id,staff_master.hname AS `name`, staff_master.designation AS designation, staff_master.contact AS contact, staff_outward.action AS action, staff_outward.date AS date FROM staff_master, staff_outward WHERE staff_master.id = staff_outward.staff_id AND staff_master.id like '%".$_GET['staff_id']."%' order by date desc");
            
           
            $b = $this->add('Grid');
            $b->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');;
            $b->addColumn('template','designation')->setTemplate('<div class="hindi"><?$designation?></div>');;
            $b->addColumn('text','contact');
            $b->addColumn('text','action');
            $b->addColumn('datetime','date');
            $b->addColumn('confirm','delete');
            $b->setSource($br);
            
            if($_GET['delete'])
            {
                $this->api->db->dsql()->table('staff_outward')->where('id ='.$_GET['delete'])->delete();
                $this->js(null,$b->js()->reload())->univ()->successMessage("Deleted")->execute();
            }
        }
    
}