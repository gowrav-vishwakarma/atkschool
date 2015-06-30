<?php

class page_data_staff_outward extends Page
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

        if ($_GET['staff_id']) 
        {
            $br = $this->api->db->dsql()
                  ->expr('SELECT staff_master.hname AS `name`, staff_master.designation AS designation, staff_master.contact AS contact, filestore_file.filename AS image FROM staff_master LEFT OUTER JOIN filestore_file ON filestore_file.id = staff_master.image WHERE staff_master.id ='.$_GET['staff_id']);
            
           $p = $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
                    ->addStyle('background', '#ddd');
            $b = $p->add('Grid');
            $b->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');;
            $b->addColumn('template','designation')->setTemplate('<div class="hindi"><?$designation?></div>');;
            $b->addColumn('text','contact');
            $b->addColumn('template', 'image')->setTemplate('<img src="upload/<?$image?>" width="100px" height="100px"/>');
            $b->setSource($br);
            
          $form=$this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
          $drp_io = $form->addField('dropdown','action');
          $drp_io->setValueList(array("inward"=>"Inward","outward"=>"Ouward"));
          
          $form->addSubmit();
          
          if($form->isSubmitted())
          {
                date_default_timezone_set('Asia/Calcutta');
                $arr = array('staff_id' => $_GET['staff_id'], 'action' => $form->get('action'), 'date' =>date('Y-m-d H:i:s'));
               $cond=$this->api->db->dsql()->expr("select action from staff_outward where staff_id = ".$_GET['staff_id']." and action in ("."'inward','outward'".")  order by id DESC")->do_getOne();
               if($cond==$form->get('action'))
               {
                    if($cond=='inward')
                    {
                       
                            $this->js()->univ()->alert('Staff is Already present --CANNOT EXECUTE INWARD')->execute();
                            return;
                     }
                    
                    else
                    {
                        
                            $this->js()->univ()->alert('Staff not present --CANNOT EXECUTE OUTWARD')->execute();
                            return;
                        
                    }
                }
          
                $this->api->db->dsql()->table('staff_outward')->set($arr)->do_insert();
                $this->js()->univ()->successMessage('Record Saved')->execute();
          }
            
    }
        else
        {
            $this->add('H1')->set('Select a staff first');
        }
    }
    
    
}
