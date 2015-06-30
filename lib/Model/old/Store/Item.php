<?php

class Model_Store_Item extends Model_Table {

    var $table = 'item_master';

    function init() {
        parent::init();
        $this->addExpression('sn')->set($this->_dsql()->expr('(select @sn :=@sn + 1 AS sn FROM (SELECT @sn := 0) as sn)'));
        $this->addField('name')->caption('Item Name');
        $cat=$this->addField('category')->setValueList(array('LVs’kujh'=>'LVs’kujh'//stationary
                                                         ,'esl'=>'esl',//mess
                                                           'LVksd'=>'LVksd'//stock
                                                           ));
        $this->addExpression('Item_rate')->set(
                $this->add('Model_Store_Inward')->dsql()->field('distinct(rate)')->where('item_id',$this->getField('id'))
                        ->where('session_id',$this->add('Model_Session_Current')->dsql()->field('id')));
        
        $this->addExpression('Total')->set(
                $this->add('Model_Store_Inward')->dsql()->field('sum(quantity)')->where('item_id',$this->getField('id'))
                        ->where('session_id',$this->add('Model_Session_Current')->dsql()->field('id')));
        
        $this->addExpression('Issued')->set(
                $this->add('Model_Store_Issue')->dsql()->field('sum(quantity)')->where('item_id',$this->getField('id'))
                        ->where('session_id',$this->add('Model_Session_Current')->dsql()->field('id')));
         $this->addExpression("in_stock")->set('id')->type('instock');
         
        
    }
    
}