<?php

class page_data_hostel_outward extends Page {

    function initMainPage() {
        parent::init();
        
        

        $f = $this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
//=======================================================================================================================
        $drp_class = $f->addField('dropdown', 'class');
        $cls = array("%" => "p;u");
        $m = $this->add('Model_Class');
        $f->getElement('class')->setAttr('class', 'hindid');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);

//=======================================================================================================================      
        $drp_std = $f->addField('dropdown', 'student')->setEmptyText('p;u');

        $r = $this->add('Model_Scholar_Current');

        $r->addCondition('ishostler', true);
        $drp_std->setModel($r, array('name'));
        $f->getElement('student')->setAttr('class', 'hindi');
        $drp_class->js('change', $f->js()->atk4_form('reloadField', 'student', array($this->api->getDestinationURL(), 'class_idx' => $drp_class->js()->val())));
        if ($_GET['class_idx']) {
            $drp_std->dq
                    ->where('class_id like ', '%' . $_GET['class_idx'] . '%')->order('fname');
        }

//===================================================================================================================
        $f->addSubmit('List');
        if ($f->isSubmitted()) {
//            $this->js()->univ()->successMessage($f->get('student'))->execute();
            $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./list'),'scholar_id'=>$f->get('student')))->execute(); 
        }
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./list'))->set('Loading..');
    }

    function page_list() {

        $this->api->stickyGET('scholar_id');

        if ($_GET['scholar_id']) 
        {
            
       

 //=========================Get Student Room and Hostel=============================================================
            $br = $this->api->db->dsql()
                  ->expr('select hostel_master.building_name as Building , rooms.room_no as Room , filestore_file.filename as image from hostel_master , rooms , hostel_allotement , student, scholars_master left OUTER JOIN filestore_file on  filestore_file.id = scholars_master.student_image where student.scholar_id = '.$_GET['scholar_id'].' AND student.id = hostel_allotement.student_id AND rooms.id = hostel_allotement.room_id AND hostel_master.id = rooms.hostel_id and scholars_master.id = student.scholar_id');
            
            $b = $this->add('Grid');
            $b->addColumn('text','Building');
            $b->addColumn('text','Room');
            $b->addColumn('template', 'image')->setTemplate('<img src="upload/<?$image?>" width="100px" height="100px"/>');
            $b->setSource($br);
 

//==============================Fetch Guardian Details=============================================================================
            $p = $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
                    ->addStyle('background', '#ddd');
            $g = $p->add('Grid');
           
            $q = $this->api->db->dsql()
                    ->expr('SELECT scholar_guardian.id,filestore_file.filename AS image,gname as guardian,relation,contact,address FROM scholar_guardian LEFT outer JOIN filestore_file on filestore_file.id=scholar_guardian.image  where scholar_id=' . $_GET['scholar_id']);
            
            $g->addColumn('text', 'guardian');
            $g->addColumn('text', 'relation');
            $g->addColumn('text', 'contact');
            $g->addColumn('text', 'address');
            $g->addColumn('template', 'image')->setTemplate('<img src="upload/<?$image?>" width="100px" height="100px"/>');
            $g->addColumn('template','guardian')->setTemplate('<div class="hindi"><?$guardian?></div>');
            $g->addColumn('template','relation')->setTemplate('<div class="hindi"><?$relation?></div>');
            $g->addColumn('template','address')->setTemplate('<div class="hindi"><?$address?></div>');
            $g->setSource($q);
//            if ($_GET['btn']) 
//            {
//                
//                $this->js('click')->univ()->frameURL('Image', array($this->api->url('master/hostel/image'), 'guardian_id' =>$_GET['btn']))->execute();
//                
//            }
//     

   //===============================================================================================================================
            $f = $this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
            $array = array('inward' => 'inward', 'outward' => 'outward', 'enquiry' => 'enquiry');//, 'card outward'=>'Card Outward','self outward'=>'Self Outward','card inward'=>'Card Inward','self inward'=>'Self Inward'
            $drp_prps = $f->addField('dropdown', 'purpose','Action');
            $drp_prps->setValueList($array);
            $f->addField('line','remarks');
            $sel = $f->addField('line', 'sel');
            $sel->js(true)->closest('.atk-form-row')->hide();

            $map = $this->add('Model_Hostel_StudentGuardian');


            $g->addSelectable($sel);

            $f->addSubmit('Save');
            if ($f->isSubmitted()) 
            
            {
                
          //==========================Check if hostel alloted=========================================== 
               $query = $this->api->db->dsql()
                            ->expr('select student_id from hostel_allotement,student where student.id=hostel_allotement.student_id and scholar_id='.$_GET['scholar_id']);
                if($query->do_getOne()==null)
                {
                    $this->js()->univ()->alert('Hostel not alloted to this student-- ALLOT HOSTEL FIRST')->execute();
                    return;
                }
                
            //======================================================================================================================
                $outw = $this->add('Model_Hostel_Outward');
                $v = json_decode($f->get('sel'));
                
                $cond=$this->api->db->dsql()->expr("select purpose from hosteller_outward where scholar_id = ".$_GET['scholar_id']." and purpose in ("."'inward','outward','card outward','self outward'".")  order by id DESC")->do_getOne();
                if($f->get('purpose')!='enquiry')
                {
                    if($cond=='inward')
                    {
                        if($f->get('purpose')=='inward' or $f->get('purpose')=='card inward' or $f->get('purpose')=='self inward')
                        {
                            $this->js()->univ()->alert('Student is Already present in Hostel--CANNOT EXECUTE INWARD')->execute();
                            return;
                        }
                    }
                    else
                    {
                        if($f->get('purpose')=='outward' or $f->get('purpose')=='card outward' or $f->get('purpose')=='self outward' )
                        {
                            $this->js()->univ()->alert('Student not present in Hostel--CANNOT EXECUTE OUTWARD')->execute();
                            return;
                        }
                    }
                }

                date_default_timezone_set('Asia/Calcutta');
                $arr = array('scholar_id' => $_GET['scholar_id'], 'withid' => $v[0], 'purpose' => $f->get('purpose'), 'date' =>date('Y-m-d H:i:s'),'remark'=>$f->get('remarks'));
               
                if($v[0]=="" )
                {
                   if($f->get('remarks')=="")
                   {
                    $this->js()->univ()->alert('Either guardian or remark must be selected')->execute();
                    return;
                   }
                   else
                {
                $this->api->db->dsql()->table('hosteller_outward')->set($arr)->do_insert();
                $this->js()->univ()->successMessage('Record Saved')->execute();
                return;
                
                } 
                }
                else
                {
                $this->api->db->dsql()->table('hosteller_outward')->set($arr)->do_insert();
                $this->js()->univ()->successMessage('Record Saved')->execute();
                }            
                
            }
        }
        
        else
        {
            $this->add("H1")->set("Select Class and Student first");
        }
    }
    
 }

