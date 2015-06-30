<?php

class Model_Store_Party extends Model_Table {

    var $table = 'party_master';

    function init() {
        parent::init();
        $this->addExpression('sn')->set($this->_dsql()->expr('(select @sn :=@sn + 1 AS sn FROM (SELECT @sn := 0) as sn)'));
        $this->addField('ename')->caption('Party Name');
        $this->addField('name')->caption('Name');
        $this->addField('contact')->caption('Contact');
        $this->addField('address')->caption('Address');
        $this->_dsql()->order('ename');
    }
}