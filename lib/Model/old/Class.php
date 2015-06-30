<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Class extends Model_Table {

    var $table = 'class_master';

    function init() {
        parent::init();
        $this->addField('class_name', 'name')->mandatory("Please give a class name")->caption('Class Name');
        $this->addField('section')->mandatory('give a class name');
        $this->addExpression('name')->set('(concat(name," - ",section))');
        $this->hasMany('SubjectClassMap','class_id');
        $this->hasMany('Student_Current', 'class_id');
        $this->hasMany('Class_Fee_Mapping','class_id');
    }

    function addFee($fee) {
        $cfm = $this->add('Model_Class_Fee_Mapping');
        $cfm->addCondition('fee_id', $fee->get('id'));
        $cfm->addCondition('class_id', $this->get('id'));
        $cfm->addCondition('session_id', $this->add('Model_Session_Current')->dsql()->field('id'));
        $cfm->tryLoadAny();
        if (!$cfm->isInstanceLoaded()) { // IF NO ENTRY IN CLASS_FEE MAPPING THEN ADD
            $cfmn = $this->add('Model_Class_Fee_Mapping');
            $cfmn->set('fee_id', $fee->get('id'));
            $cfmn->set('class_id', $this->get('id'));
            $cfmn->save();
        }

        if (!$fee->get('isOptional')) {
            foreach ($this->ref('Student_Current') as $std) {
                $s = $this->add('Model_Fee_Applicable');
                $s->addCondition('student_id', $std['id']);
                $s->addCondition('fee_id', $fee->get('id'));
                $s->tryLoadAny();
                if (!$s->isInstanceLoaded()) { //IF THIS FEE IS NOT ALREADY APPLIED ON THIS STUDENT THEN APPLY
                    $s->unload();
                    $s->set('student_id', $s['id']);
                    $s->set('fee_id', $fee->get('id'));
                    if ($std['isScholared'])
                        $s->set('amount', $fee->get('scholaredamount'));
                    else
                        $s->set('amount', $fee->get('amount'));
                    $s->set('due',$s->get('amount'));
                    $s->save();
                }
                $s->destroy();
            }
        }
    }

    function isFeeAssociated($fee) {
        $cf = $this->add('Model_Class_Fee_Mapping');
        $cf->addCondition('fee_id', $fee->get('id'));
        $cf->addCondition('class_id', $this->get('id'));
        $cf->tryLoadAny();
        if ($cf->loaded())
            return true;
        else
            return false;
    }

    function removeFee($fee) {
        if (!$this->isFeeAssociated($fee))
            return;
        foreach ($this->ref('Student_Current') as $student) {
            $fa = $this->add('Model_Fee_Applicable');
            $fa->addCondition('student_id', $student['id']);
            $fa->addCondition('fee_id', $fee->get('id'));
            $fa->tryLoadAny();
            if ($fa->loaded())
                throw $this->exception("This Fee " . $fee->get('name') . " is Applicable on Student(s)... Remove it from all students first");
        }
        $cf = $this->add('Model_Class_Fee_Mapping');
        $cf->dsql()->where('class_id', $this->get('id'))->where('fee_id', $fee->get('id'))->delete();
    }
   
                
    function setSubjects($ids)
    {
        
        if($ids==null)return;
        $ss=$this->add('Model_Session_Current')->loadAny();
    	foreach($ids as $id){
    		$res[]=array('subject_id'=>$id, 'class_id'=>$this->id, 'session_id'=>$ss->id);
    	}
    	$this->ref('SubjectClassMap')->dsql()->insertAll($res);
    }
}

?>
