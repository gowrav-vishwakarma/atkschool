<?php
class page_student_report extends Page{

	public $field_list=array('sno','roll_no','scholar_no','class','name','fname','father_name','efather_name','mother_name','emother_name','guardian_name','age','admission_date','isScholared','ishostler', 'dob','contact','p_address','sex','category','image_url');

	function page_index(){
        
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form',null,null,array('form_empty'));
		$class=$form->addField('dropdown','class_field')->setEmptyText('----')->setAttr('class','hindi');
		$c=$this->add('Model_Class');
		$class->setModel($c);
		$s=$form->addField('dropdown','filter_sex')->setValueList(array("-1"=>"Any",
																"M"=>"Male",
																"F"=>"Female"))->set('-1');
		$cat=$form->addField('dropdown','filter_category')->setValueList(array("-1"=>'Select Category', 
																		'GEN'=>'GEN',
																		"ST"=>"ST",
																		"SC"=>"SC",
                                                                        "TAD"=>"TAD(ST)",
																		"TAD with ST"=>"TAD with ST",
																		"OBC"=>"OBC",
                                                                        "EBC"=>"EBC",
                                                                        "SBC"=>"SBC",
                                                                        "EBC(SBC OBC)"=>"EBC(SBC & OBC)",
																		"SOBC"=>"SPECIAL OBC",
																		"MINORITY"=>"MINORITY"))->set('-1');
	
		$h=$form->addField('dropdown','hostel')->setValueList(array("-1"=>"Any",
																	"0"=>"Local",
																	"1"=>"Hosteler"))->set('-1');																
		$b=$form->addField('dropdown','bpl')->setValueList(array("-1"=>"Any",
																	"0"=>"No",
																	"1"=>"Yes"))->set('-1');																
        $sc=$form->addField('dropdown','scholar')->setValueList(array("-1"=>"Any",
                                                                    "0"=>"Private",
                                                                    "1"=>"Scholared"))->set('-1');                                                              
        $from_age=$form->addField('line','from_age');//->js(true)->univ()->numericField();

        $to_age=$form->addField('line','to_age');//->js(true)->univ()->numericField();

        foreach($this->field_list as $f){
            $form->addField('checkbox',$f);
        }

        $form->addSubmit('Print');


      if($form->isSubmitted())
      {
        $chk_values=array();
        foreach($this->field_list as $f){
            $chk_values += array($f => $form->get($f));
        }
        $form_values=array("to_age"=>$form->get('to_age'),"from_age"=>$form->get('from_age'),"class_drp"=>$form->get('class_field'),"filter_sex"=>$form->get('filter_sex'),"filter_category"=>$form->get('filter_category'),"hostel"=>$form->get('hostel'),"scholar"=>$form->get('scholar'),"bpl"=>$form->get('bpl'));
        $total_values=$form_values + $chk_values;       
       $this->js()->univ()->newWindow($this->api->url("./studentlist",$total_values),null,'height=689,width=1246,scrollbar=1')->execute();
      }
      
    }


    function page_studentlist(){


        $this->api->stickyGET('class_drp');
        $this->api->stickyGET('sex');
        $this->api->stickyGET('category');
        $this->api->stickyGET('hostel');
        $this->api->stickyGET('scholar');
        $this->api->stickyGET('bpl');
        $this->api->stickyGET('to_age');
        $this->api->stickyGET('from_age');

        $grid=$this->add('Grid');


        $m=$this->add('Model_Scholars_Current');
     

        $m->addExpression('guardian_name')->set("".$m['guardian_name']."")->display(array('grid'=>'hindi'));
        // $m->addExpression('guardian_name')->set(function($m,$q){
        //     $m1=$m->add('Model_Scholars_Guardian');
        //     $m1->addCondition('scholar_id',$m->getField('id'));
        //     $m1->_dsql()->limit(1)->order($m1->getField('id'),'desc');
        //     return $m1->fieldQuery('gname');
        // })->display(array('grid'=>'hindi'));
    
        if($_GET["class_drp"]){
            $m->addCondition('class_id',$_GET['class_drp']);
        }
        if($_GET["filter_sex"]!="-1"){
             $m->addCondition('sex',$_GET['filter_sex']);
        }
         if($_GET['filter_category']!="-1")
        {
            if($_GET['filter_category']=='TAD with ST')
            {
                $m->addCondition('category',array('ST','TAD'));
                //$m->addCondition('category','TAD');
            }elseif ($_GET['filter_category']=='EBC(SBC OBC)') {
                $m->addCondition('category',array('EBC','SBC','OBC'));
                # code...
            }
            else
            $m->addCondition('category',$_GET['filter_category']);
        }

        if($_GET['hostel']!="-1")
        {
          $m->addCondition('ishostler',$_GET['hostel']);   
        }
        if($_GET['scholar']!="-1")
        {
            $m->addCondition('isScholared',$_GET['scholar']);
        }
        if($_GET['bpl']!="-1")
        {
            $m->addCondition('bpl',$_GET['bpl']);
        }

        if($_GET['to_age'])
            $m->addCondition('age','<=',$_GET['to_age']);
        if($_GET['from_age'])
            $m->addCondition('age','>=',$_GET['from_age']);
        // $m->debug();

        $m->_dsql()->del('order')->order('roll_no');

        $display_array=array();
        foreach($this->field_list as $f){
            if($_GET[$f]) $display_array[] = $f;
        }
        if($_GET['sno']) $grid->addColumn('sno','sno');
        // $m->debug();
        // print_r($display_array);
        $grid->setModel($m,array_merge($display_array),array('class'));
          // $grid->setFormatter('class','hindi');
          // $m->debug();
        // $grid->add('  misc/Export');
    
    }
}