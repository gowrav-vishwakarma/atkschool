
<?php

class page_master_hostel_allotment1 extends Page {

    function initMainPage() {
        parent::init();
        $f = $this->add('Form');
        
        


        
        $drp_class = $f->addField('dropdown', 'class');
        $cls = array("%" => "Select Class");
        $m = $this->add('Model_Class');
        foreach ($m as $a) {
            $cls+= array($a['id'] => $a['name']);
        }
        $drp_class->setValueList($cls);

        $f->addSubmit('List Students');
        $this->api->stickyGET('class');
   /*     $g = $this->add('MVCGrid');
        $r = $this->add('Model_Student_Current');
        //$r->hasOne('Scholar','scholar_id');
        $r->addCondition('ishostler', true);
        $g->setModel($r, array('scholar'));
        $g->addColumn('expander', 'allot');
        //$g->addCondition('ishostler',true);
        if ($_GET['name'])
            $g->dq->where('class_id like ', '%' . $_GET['name'] . '%');         */

        if ($f->isSubmitted()) 
        {
               $this->js()->find('.atk4_loader')->atk4_loader('loadURL', array($this->api->url('./sub'),'class_id'=>$f->get('class')))->execute();
//            $g->js(null, $g->js()->reload(array("name" => $f->get('class'))))->execute();
        }
        $this->add('View')->js(true)->atk4_load($this->api->url('./sub'))->set('Loading..');
    }

    function page_allot() {
        $p = $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')
                ->addStyle('background', '#ddd');
        $this->api->stickyGET('student_id');
        $this->api->stickyGET('session_id');
        $this->api->stickyGET('room');
        $f = $p->add('Form');
        $drp_hstl = $f->addField('dropdown', 'hostel');

        $r = $this->add('Model_Hostel_Rooms');

        $drp_room = $f->addField('dropdown', 'room', 'Room Number');
        $drp_room->setModel($r, array('room_no'));

        $hstl = array("%" => "Select Hostel");
        $m = $this->add('Model_Hostel');
        foreach ($m as $a) {

            $hstl+= array($a['id'] => $a['building_name']);
        }
        $drp_hstl->setValueList($hstl);
        $drp_hstl->js('change', $f->js()->atk4_form('reloadField', 'room', array($this->api->getDestinationURL(), 'hostel_idx' => $drp_hstl->js()->val())));
        // $line= $f->addField('line','remaining');




        if ($_GET['hostel_idx']) {
            $drp_room->dq
                    ->where('hostel_id like ', '%' . $_GET['hostel_idx'] . '%');
        }


        $f->addSubmit('Save');


        if ($f->isSubmitted()) {

            //--------------------------Retrive number of students alloted to a room in a session-----------------------------

            $ds = $this->add('Model_Session_Current')->dsql()->field('id');
            $session = $ds->do_getOne();
            $dq = $this->api->db->dsql()->table('hostel_allotement')->field('count(*)');
            $dq->where('room_id =' . $f->get('room') . ' and session_id = ' . $session);

            $count = $dq->do_getOne();

            //---------------------------Retrive capacity of the room---------------------------------------------------

            $bq = $this->api->db->dsql()->table('rooms')->field('capacity');
            $bq->where('id =' . $f->get('room'));
            global $capacity;
            $capacity = $bq->do_getOne();
            $remaining = $capacity - $count - 1;
            //     $drp_room->js('change',$f->js()->atk4_form('reloadField','remaining',array($this->api->getDestinationURL(),$line->setValue($remaining))));  
            //--------------------------Checking for duplicacy of student in a session-------------------------------------
            $cq = $this->api->db->dsql()->table('hostel_allotement')->field('count(*)')
                    ->where('student_id=' . $_GET['student_id'] . ' and session_id=' . $session);
            $cnt = $cq->do_getOne();
            if ($cnt > 0) {
                $this->js()->univ()->alert('Student has been already alloted a room')->execute();
                return;
            }
            //---------------------------------Check if room is full or not---------------------------------------------

            if ($count >= $capacity) {
                $this->js()->univ()->alert('Room is already Full')->execute();
                return;
            } else {
                $save = $this->add('Model_Hostel_Allotment');
                $save->set('room_id', $f->get('room'));
                $save->set('student_id', $_GET['student_id']);

                $save->save();
                $this->js()->univ()->successMessage("HOSTEL ALLOTED" . " and remaining =" . $remaining)->execute();
            }
        }
    }
    
    function page_sub() 
    {
        if(!$_GET['class_id'])
        {
            $this->add('H3')->set("Please select a class first ");
            return;
        }
       
        
     /*   $x=$this->add('Form');
        $y=$x->add('Button','hi');
        $y->js('click',$this->js()->univ()->successMessage("Hi There".$_GET['class_id']));      */
        
        $dq = $this->add('Model_Student_Current')
                    ->addCondition('class_id',$_GET['class_id'])
                    //->addCondition('ishostler',true)
                    ;
        
        $cls = array("%" => "Select Class");    
        foreach ($dq as $a) 
        {
           $cls+= array($a['id'] => $a['scholar']);
           $f=$this->add('Form',NULL,NULL,ARRAY('form_horizontal'));
           $f->addField('line', 'student')->set($a['scholar']);
           $drp_hstl=$f->addfield('dropdown','hostel');
           
        $hstl = array("%" => "Select Hostel");
        $m = $this->add('Model_Hostel');
        foreach ($m as $a) 
             $hstl+= array($a['id'] => $a['building_name']);
        $drp_hstl->setValueList($hstl);
        
        $r = $this->add('Model_Hostel_Rooms');

        $drp_room = $f->addField('dropdown', 'room', 'Room Number');
        $drp_room->setModel($r, array('room_no'));
        
        if ($_GET['hostel_idx']) 
        {
            $drp_room->dq
                    ->where('hostel_id like ', '%' . $_GET['hostel_idx'] . '%');
        }
        
        $drp_hstl->js('change', $f->js()->atk4_form('reloadField', 'room', array($this->api->getDestinationURL(), 'hostel_idx' => $drp_hstl->js()->val())));
        
        $f->addSubmit('Allot');
        }
//        $f=$this->add('Form');
//        $drp = $f->addField('dropdown', 'students');
//        $drp->setValueList($cls);
//        
    }

}