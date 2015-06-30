<?php

class Model_Subject extends Model_Table{

    var $table = 'subject_master';

    function init() 
    {
    
        parent::init();
        $this->addField('name')->mandatory('subject can not be blank')->display('hindi');
        $this->addField('code');
       // $this->addField('max_marks')->type('int');
       //$this->addField('pass_marks')->type('int');

        // $this->hasMany('RelatedClass','subject_id');
        $this->hasMany('SubjectClassMap','subject_id');
        $this->hasMany('ExamClassSubjectMap','subject_id');
        
        $this->addHook('beforeSave',$this);
        $this->addHook('beforeDelete',$this);

    }
    function beforeSave(){
        $this->add('Controller_Unique',array('unique_fields'=>array('name'=>$this['name'])));

    }

    function beforeDelete(){
        $s=$this->add('Model_SubjectClassMapAll');
        $s->addCondition('subject_id',$this->id);
        if($s->count()->getOne())
            throw $this->exception("This Subject has Associated classes in Any of Session, Cannot delete");


    }
}