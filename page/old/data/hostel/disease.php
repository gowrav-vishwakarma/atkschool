<?php

class page_data_hostel_disease extends Page
{
    function initMainPage()
    {
        parent::init();
        
        $f = $this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
//=======================================================================================================================
        $drp_class = $f->addField('dropdown', 'class');
        $cls = array("%" => "p;u");
        $m = $this->add('Model_Class');
        $f->getElement('class')->setAttr('class', 'hindi');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);

//=======================================================================================================================      
        $drp_std = $f->addField('dropdown', 'scholar')->setEmptyText('p;u');
        $r = $this->add('Model_Scholar_Current');
        
       

       $r->addCondition('ishostler', true);
        //$r = $this->api->db->dsql()->expr("SELECT \"%\" AS id, \"ANY\" AS `name` UNION SELECT scholars_master.id, `fname` FROM scholars_master, class_master, session_master, student WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND class_master.id LIKE '%' AND session_master.iscurrent = TRUE");
        $drp_std->setModel($r, array('name'));
        $f->getElement('scholar')->setAttr('class', 'hindi');
        $drp_class->js('change', $f->js()->atk4_form('reloadField', 'scholar', array($this->api->getDestinationURL(), 'class_idx' => $drp_class->js()->val())));
        if ($_GET['class_idx']) {
            
                $drp_std->dq
                    ->where('class_id like ', '%' . $_GET['class_idx'] . '%')->order('fname');
        }
        
        $drp_dis=$f->addfield('dropdown','disease')->setAttr('class', 'hindi');
        $drp_dis->setValueList(array('cq[kkj'=>'cq[kkj'/*bukhar*/,
                  '[kk¡lh'=>'[kk¡lh'/*khansi*/,
                  'tq[kke'=>'tq[kke'/*jukham*/,
                  'ÅYVh '=>'ÅYVh '/*ulti*/,
                'nLr'=>'nLr'/*dast*/,
                'flj nnZ'=>'flj nnZ'/*sir dard*/,
                'pDdj'=>'pDdj'/*chaker*/,
                'pksV'=>'pksV'/*chot*/,
                'vk¡[k&dku nnZ'=>'vk¡[k&dku nnZ'/*aankh kan dard*/,
            '?kcjkgV'=>'?kcjkgV'/*ghabrahat*/,
            'gkFk&iSj nnZ'=>'gkFk&iSj nnZ'/*hath per dard*/,
            'ÅYVh&nLr'=>'ÅYVh&nLr'/*ulti dast*/,
            'lnhZ&tq[kke'=>'lnhZ&tq[kke'/*sardi jhukham*/,
            'Ropk jksx'=>'Ropk jksx'/*twacha rog*/,
            'isV nnZ'=>'isV nnZ'/*pet dard */,
            'lwtu'=>'lwtu'/*sujan*/,
            'Nkys'=>'Nkys'/*chale*/,
                ));
        //$drp_trt=$this->addField('dropdown','Treatment')->setValueList(array('%'=>'All','1'=>'Treated','0'=>'Not Treated'));

         $drp_trt=$f->addField('dropdown','treated')->setValueList(array('%'=>'All',0=>'Not Treated',1=>'Treated'));
         $drp_trt->js('change',$this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./list'),'treated'=>$drp_trt->js()->val())));

        $f->addSubmit();
        
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./list'))->set('Loading..');
        
        if($f->isSubmitted())
        {
                date_default_timezone_set('Asia/Calcutta');
                $arr = array('scholar_id' => $f->get('scholar'), 'disease' => $f->get('disease'), 'report_date' =>date('Y-m-d H:i:s'),'treatment'=>0);
                $this->api->db->dsql()->table('disease_master')->set($arr)->do_insert();
                $this->js()->univ()->successMessage('Record Saved')->execute();
        }

    }
    
    function page_list_details()
    {
        date_default_timezone_set('Asia/Calcutta');
        $this->api->stickyGET('id');
        $p = $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
                    ->addStyle('background', '#ddd');
       $crud= $p->add('CRUD');
       $crud->setModel('Hostel_Treatment',array('disease_id','name'),array('name','treatment_date'));
       if($crud->form)
       {
       $crud->form->getElement('disease_id')->set($_GET['id']);
       $crud->form->getElement('disease_id')->js(true)->hide();
       }
       $crud->getModel()->addCondition('disease_id',$_GET['id']);
        
    }
    
    function page_list()
    {
        $this->api->stickyGET('treated');
        //$this->add('H1')->set('hi'.$_GET['treated']);
        $g=$this->add('Grid');
        if($_GET['treated'])
            $b = $this->api->db->dsql()->expr("SELECT faketable.* , @sn :=@sn + 1 as sn FROM ( SELECT disease_master.id AS id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM  scholars_master, class_master, hostel_master, rooms, disease_master, student LEFT OUTER JOIN hostel_allotement ON hostel_allotement.student_id = student.id WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id AND disease_master.scholar_id = scholars_master.id AND disease_master.treatment like '".$_GET['treated']."%' UNION SELECT disease_master.id AS id, scholars_master.fname AS `name`, class_master.`name` AS class, \"\" AS hostel, \"\" AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM scholars_master, class_master, disease_master, student WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND student.id NOT IN ( SELECT hostel_allotement.student_id FROM hostel_allotement ) AND disease_master.scholar_id = scholars_master.id ) AS faketable , (SELECT @sn := 0) AS sn ORDER BY faketable.report DESC");
        else
            $b = $this->api->db->dsql()->expr("SELECT faketable.* , @sn :=@sn + 1 as sn FROM ( SELECT disease_master.id AS id, scholars_master.hname AS `name`, class_master.`name` AS class, hostel_master.building_name AS hostel, rooms.room_no AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM scholars_master, class_master, hostel_master, rooms, disease_master, student LEFT OUTER JOIN hostel_allotement ON hostel_allotement.student_id = student.id WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND hostel_allotement.room_id = rooms.id AND rooms.hostel_id = hostel_master.id AND disease_master.scholar_id = scholars_master.id AND disease_master.treatment like '0%' UNION SELECT disease_master.id AS id, scholars_master.fname AS `name`, class_master.`name` AS class, \"\" AS hostel, \"\" AS room, disease_master.disease AS disease, disease_master.report_date AS report, disease_master.treatment AS treatment FROM scholars_master, class_master, disease_master, student WHERE scholars_master.id = student.scholar_id AND student.class_id = class_master.id AND student.id NOT IN ( SELECT hostel_allotement.student_id FROM hostel_allotement ) AND disease_master.scholar_id = scholars_master.id ) AS faketable , (SELECT @sn := 0) AS sn ORDER BY faketable.report DESC");            
        $g->addColumn('text','sn');
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        $g->addColumn('text','class');
        $g->addColumn('text','hostel');
        $g->addColumn('text','room');
        $g->addColumn('template','disease')->setTemplate('<div class="hindi"><?$disease?></div>');
        $g->addColumn('datetime','report');
        $g->addColumn('boolean','treatment');
        
       
       // $drp_trt>js("change",$g->js()->reload(array('trtmnt'=>$drp_trt->js()->val())));

        
        if($_GET['isTreated'])
        {
           $t= $this->api->db->dsql()->table('disease_master')->field('treatment')->where('id',$_GET['isTreated'])->getOne();
            $this->api->db->dsql()->table('disease_master')->set('treatment',!$t==true?1:0)->where('id',$_GET['isTreated'])->update();
             $this->js(null,$g->js()->reload())->univ()->execute();
        }
        
        if($_GET['delete'])
        {
            $this->api->db->dsql()->table('disease_master')->where('id',$_GET['delete'])->delete();
             $this->js(null,$g->js()->reload())->univ()->successMessage("Deleted")->execute();
        }
        $g->setSource($b);
        $g->addColumn('expander','details');
        $g->addColumn('button','isTreated');
        $g->addColumn('confirm','delete');
        
        $g->addColumn('template','class')->setTemplate('<div class="hindi"><?$class?></div>');
    }
    
}