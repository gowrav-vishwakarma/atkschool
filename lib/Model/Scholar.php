<?php

class Model_Scholar extends Model_Table {
	var $table='scholars_master';
	
	function init(){
		parent::init();

        	$this->addField('admission_date')->type('date')->mandatory("Required Field")->defaultValue(date('d-m-Y'));
                $this->addField('scholar_no')->mandatory("Scholar Number is Must")->sortable(true)  ;
                $this->addField('fname')->mandatory("Name is Must")->caption('Name(English)');
                $this->addField('hname')->mandatory("Name is Must")->caption('Name(Hindi)')->display('hindi');
                $this->addField('father_name')->mandatory("Required Field")->caption('Father`s Name')->display('hindi');
                //$this->add("filestore/Field_Image","f_image")->caption('father Image');

                $this->addField('mother_name')->mandatory("Required Field")->caption('Mother`s Name')->display('hindi');                
                //$this->add("filestore/Field_Image","m_image")->caption('Mother Image');
                // $this->addField('guardian_name');
                $this->addField('dob')->type('date')->mandatory("Required Field")->caption('Date of Birth');
                $this->addField('contact')->mandatory("Required Field");
                $this->add("filestore/Field_Image","student_image")->type('image');//$this->add("filestore/Field_Image", "student_image"); 
                $this->addField('p_address')->datatype('Text')->mandatory("Required Field")->caption('Permanent Address')->display('hindi');;
                $this->addField('sex')->setValueList(array('M'=>'Male','F'=>'Female'))->mandatory("This Field is Must");
                $this->addField("isActive")->type('boolean')->mandatory("Is This Active")->defaultValue(true);
                $this->addField('leaving_date')->type('date')->defaultValue(null);
                $this->addField('previouse_school_name')->display('hindi');
                $this->addField('previouse_class_name')->display('hindi');
                $this->addField('category')->setValueList(array(null=>'Select Category', 'GEN'=>'GEN',"ST"=>"ST","SC"=>"SC","TAD"=>"TAD(ST)","OBC"=>"OBC","SOBC"=>"SPECIAL OBC","MINORITY"=>"MINORITY"))->mandatory("category is must");
                
                $this->hasMany('Student','scholar_id');
                $this->hasMany('Scholars_GuardianAll','scholar_id');
                $this->hasMany('Scholars_Guardian','scholar_id');
                // $this->hasMany('Scholar','scholar_id');
                // $this->hasMany('Students_Movement','scholar_id');
                $this->hasMany('Disease','scholar_id');
                $this->_dsql()->order('scholar_no','asc');
                $this->_dsql()->del('order')->order('fname','asc');

                $this->addExpression('name')->set('hname')->display('hindi');
                // $this->addExpression('Student_name')->set('fname');
                $this->addExpression('age')->set('year(now())-year(dob)');

                $fs=$this->leftJoin('filestore_file','student_image')
                        ->leftJoin('filestore_image.original_file_id')
                        ->leftJoin('filestore_file','thumb_file_id');
                $fs->addField('image_url','filename')->display(array('grid'=>'pic'))->system(false);

                $this->addExpression('active_in_session')->set(function($m,$q){
                        return $m->refSQL('Student')->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'))->count();
                })->type('boolean');

                // $this->_dsql()->order('fname','asc');

                $this->addHook('beforeDelete',$this);
                $this->addHook('beforeSave',$this);


	}
        function beforeSave(){
                $s=$this->add('Model_Scholar');
                
                if($this->loaded()){
                        // editing
                        $s->addCondition('id','<>',$this->id);
                        
                }else{
                        // Adding
                }
                
                $s->addCondition('scholar_no',$this['scholar_no']);
                $s->tryLoadAny();
                if($s->loaded()){
                        throw $this->exception("This scholar no is Already Exist");
                }
        }

        function beforeDelete(){


                if($this->ref('Student')->count()->getOne() > 0){
                        throw $this->exception("You can not Delete Scholar, It is a Student");
                        
                }
                
        }
        
}