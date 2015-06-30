<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_Scholar_Current extends Model_Scholar {

    function init() {
        parent::init();
        
        $st = $this->join('student.scholar_id');
        $st->hasOne('Class', 'class_id');
        $st->addField('ishostler')->type('boolean');
        $st->addField('isScholared')->type('boolean')->caption('Hostler As Scholared');
        $st->addField('bpl')->type('boolean')->caption('BPL');
        $st->hasOne('Session_Current', 'session_id');

        //$g = $st->join('scholar_guardian.scholar_id', null, 'left');
        $this->addCondition('session_id', $this->add('Model_Session_Current')->dsql()->field('id'));
        // $this->_dsql()->order(array('class_id','fname'));
        
    }

    function associateClassFeeses() {
        $st = $this->add('Model_Student_Current');
        $st->addCondition('session_id', $this->add('Model_Session_Current')->dsql()->field('id')->getOne());
        $st->addCondition('scholar_id', $this->id);
        $st->loadAny();
        $fee = "";
        foreach ($this->add('Model_Class')->load($st->get('class_id'))->ref('Class_Fee_Mapping') as $f) {
            $fee=$this->add('Model_Fee')->load($f['fee_id']);
            $fapp=$this->add('Model_Fee_Applicable');
            $fapp->set('student_id',$st->id);
            $fapp->set('fee_id',$fee->id);
            $fapp->set('amount',($this->get('isScholared')?$fee->get('scholaredamount'):$fee->get('amount')));
            $fapp->set('due',$fapp->get('amount'));
            $fapp->save();
            $fee->destroy();
            $fapp->destroy();
        }
        return $fee;
    }
}