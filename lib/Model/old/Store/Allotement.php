<?php

class Model_Store_Allotement extends Model_Table {

    var $table = 'student';

    function init() {
        parent::init();
        
        $this->addExpression('sn')->set($this->_dsql()->expr('(select @sn :=@sn + 1 AS sn FROM (SELECT @sn := 0) as sn)'));
        $this->addField('store_no');
        $m=$this->join('scholars_master','scholar_id');
        $this->hasOne('Scholar','scholar_id');
        $m->addField('father_name');
      
        $this->addField('isScholared')->type('boolean');
        $this->addField('ishostler')->system(true);
        $this->hasOne('Class','class_id');
        $this->hasOne('Session_Current','session_id');
        $this->addCondition('session_id',
                $this->add('Model_Session_Current')->dsql()->field('id'));
        $this->addCondition('ishostler',true);
        $this->_dsql()->order('store_no');
    }
}