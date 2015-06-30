<?php

class Model_Exam  extends Model_Table
{
    
    var $table='exam_master';
    function init()
    {
        parent::init();
        $this->addField('name')->display('hindi');
        $this->hasMany("ExamClassMap","exam_id");

        $this->addHook('beforeDelete',$this);
    }

    function beforeDelete(){
    	if($this->ref('ExamClassMap')->count()->getOne())
    		throw $this->exception('This Exam has Associated Classes, remove them first to remove this Exam');


        if($this->ref('ExamClassMapAll')->sum('min_marks')->getOne())
            throw $this->exception('This Exam has Min Marks Classes, remove the marks first');
        
        if($this->ref('ExamClassMapAll')->sum('max_marks')->getOne())
            throw $this->exception('This Exam has Max Marks Classes, remove the marks first');
   

    }

    function addClass($class,$session=null){
        if(!$class instanceof Model_Class)
            throw $this->exception(' addSubject must be passed Loaded Subject Object');

        if($session==null) $session=$this->add('Model_Sessions_Current')->tryLoadAny();

        if($this->hasClass($class,$session))
            throw $this->exception('Already in the class');       


        $newexam = $this->add('Model_ExamClassMapAll');
        $newexam->createNew($this,$class,$session);
    }

    function hasClass($class,$session=null){
        if(!$session) $session = $this->add('Model_Sessions_Current')->tryLoadAny();

        $exam = $this->add('Model_ExamClassMapAll');
        $exam->addCondition('class_id',$class->id);
        $exam->addCondition('session_id',$session->id);
        $exam->addCondition('exam_id',$this->id);
        $exam->tryLoadAny();

        if($exam->loaded())
            return $exam;
        else
            return false;
    }


    function removeClass($class,$force=false){

        if(!$class instanceof Model_Class)
            throw $this->exception(' removeClass must be passed Loaded Class Object');

        if(!($exam_in_class=$this->hasClass($class))) // returning SubjectInClass --- Not subject Model
            throw $this->exception('This Subject is not Available in Class');

        $exam_in_class->delete();
    }

}