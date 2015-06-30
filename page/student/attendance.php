<?php

class page_student_attendance extends Page{
	function page_index(){
		// parent::init();
        // $this->api->stickyGET('filter');
        // $this->api->stickyGET('month');
        // $this->api->stickyGET('att');
        $acl=$this->add('xavoc_acl/Acl');
        try{
    		$form=$this->add('Form',null,null,array('form_horizontal'));
    		$class_field=$form->addField('dropdown','class')->setEmptyText('----')->setNotNull()->setAttr('class','hindi');
       	 	$cm=$this->add('Model_Class');
    		$class_field->setModel($cm);
    		$month=$form->addField('dropdown','month')->setEmptyText('----')->setNotNull();
            $month->setValueList(array('1'=>'January',
            							'2'=>'February',
            							'3'=>'March',
            							'4'=>'April',
            							'5'=>'May',
            							'6'=>'Jun',
            							'7'=>'July',
            							'8'=>'August',
            							'9'=>'September',
            							'10'=>'October',
            							'11'=>'November',
            							'12'=>'December'
            							));
             $att=$form->addField('line','att','Total Monthly Attendance');
            $form->addField('checkbox','change_total_attendance');
            $att->js(true)->univ()->numericField()->disableEnter();
            $form->addSubmit('Allot');
        
            if($form->isSubmitted()){
                $sam=$this->add('Model_Students_Attendance');
                $sam->addCondition('class_id',$form->get('class'));
                $sam->addCondition('month',$form->get('month'));
                $sam->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
                // $sam->debug();
                $students_in_attendance_table_for_this_class= $sam->count()->getOne();
                $c=$this->add('Model_Class');
                $c->load($form->get('class'));
                $total_students_in_class=$c->ref('Students_Current')->count()->getOne();
                if($total_students_in_class != $students_in_attendance_table_for_this_class){
                    if($form->get('att') == null ){
                        $form->displayError('att','New Students Found to be added, Kindly give total monthly attendance again'. $total_students_in_class .'and'.$students_in_attendance_table_for_this_class);
                    }
                    foreach($c->ref('Students_Current') as $junk){
                        // Check every students existance, if not found add this student's entry in attendance table
                        $existing=$this->add('Model_Students_Attendance');
                        $existing->addCondition('class_id',$form->get('class'));
                        $existing->addCondition('month',$form->get('month'));
                        $existing->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
                        $existing->addCondition('student_id',$junk['id']);
                        $existing->tryLoadAny();

                        if(!$existing->loaded()){
                            $sam['student_id']=$junk['id'];
                            $sam['total_attendance']=$form->get('att');
                            $sam->saveAndUnload();
                        }
                        $existing->destroy();
                    }
                }else{
                    if($form->get('att') and $form->get('change_total_attendance')==false)
                        $form->displayError('att','Please Check the CheckBox for Fill Attendance');
                    if($form->get('att') and $form->get('att')!=$sam['total_attendance'] and $form->get('change_total_attendance') == true){
                        $sam->unload();
                        $sam->_dsql()->set('total_attendance',$form->get('att'))->update();
                    }
                }

                $form->js(null,$form->js()->reload())->univ()->newWindow($this->api->url("./attinput",array('class'=>$form->get('class'),'month'=>$form->get('month'),'att'=>$form->get('att'))),null,'height=689,width=1246,scrollbar=1')->execute();
            }		
        }catch(Exception $e){
            $this->js()->univ()->errorMessage($e->getMessage())->excute();

        }
	}

    function page_attinput(){

        $this->api->stickyGET('class');
        $this->api->stickyGET('month');
        $this->api->stickyGET('att');


        $grid=$this->add('Grid');
        $sa=$this->add('Model_Students_Attendance');
        $sa->addCondition('class_id',$_GET['class']);
        $sa->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
        $sa->addCondition('month',$_GET['month']);
        $grid->setModel($sa,array('roll_no','class','student','month','total_attendance','present'));
        $sa->_dsql()->del('order')->order('roll_no','asc');

        $grid->setFormatter('student','hindi');


        // $s=$this->add('Model_Student');
        // $s->addCondition('class_id',$_GET['class']);
        // $s->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));


        // // $s=$this->add('Model_Student');
        // $s->addExpression('all_meeting')->set(function($m,$q){
        //     return $m->refSQL('Students_Attendance')->sum('total_attendance');
        // });
        // $s->addExpression('all_attendance')->set(function($m,$q){
        //     return $m->refSQL('Students_Attendance')->sum('present');
        // });
        // // $s->addExpression('month')->set(function($m,$q){
        // //     return $m->refSQL('Students_Attendance')->fieldQuery('month');
        // // });
        // $s->addExpression('total_attendance')->set(function($m,$q){
        //     return $m->refSQL('Students_Attendance')->addCondition('month',$_GET['month'])->sum('total_attendance');
        // });
        // $s->addExpression('present')->set(function($m,$q){
        //     return $m->refSQL('Students_Attendance')->addCondition('month',$_GET['month'])->sum('present');
        // });

        // // $s->addCondition('month',$_GET['month']);
        // $grid->setModel($s,array('roll_no','class','name','all_meeting','all_attendance','total_attendance','present'));
        // $s->_dsql()->del('order')->order('roll_no','asc');
        // // if($crud->grid){
        // // $grid->setFormatter('student','hindi');
        // $grid->setFormatter('class','hindi');
        // // $grid->addColumn('expander','edit');
        $grid->addFormatter('present','grid/inline');

        // // $this->api->template->tryDel('logo');
        $this->api->template->tryDel('header');

    }

}