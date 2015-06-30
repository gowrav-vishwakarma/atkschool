<?php

class Model_Store_Inward extends Model_Table
{
var $table='item_inward';
    
    function init()
    {
        parent::init();
        $this->addField('bill_id');
        $this->hasOne('Store_Item','item_id');
        //$this->addField('date')->type('date')->defaultValue(date('d-m-Y'));  
       
       $this->addField('quantity');
       $this->addField('rate');
       $this->hasOne('Session_Current','session_id');
       $this->addCondition('session_id',$this->add('Model_Session_Current')->dsql()->field('id'));
       $this->addExpression('amount')->set('id')->type('mul');
       $this->addHook('beforeDelete',$this);
        
    }
    
    
    function beforeDelete()
    {
       // $this->api->stickyGET('student_id');
       $total= $this->api->db->dsql()->expr("SELECT sum(quantity) FROM item_inward WHERE item_id in (select item_id from item_inward where id = ".$this['id'].") AND session_id IN ( SELECT id FROM session_master WHERE iscurrent = TRUE )")->getOne();
        $issue=$this->api->db->dsql()->expr("SELECT sum(item_issue.quantity) FROM item_issue,item_inward WHERE item_issue.item_id=item_inward.item_id and item_inward.id = ".$this['id']." AND item_issue.session_id IN ( SELECT id FROM session_master WHERE iscurrent = TRUE )")->getOne();
    $q= $this->api->db->dsql()->expr("SELECT quantity FROM item_inward WHERE id = ".$this['id']." AND session_id IN ( SELECT id FROM session_master WHERE iscurrent = TRUE )")->getOne();
    $sum=$total-$issue-$q;
        if($sum<0)
        {
    
            throw $this->exception("Cannot Delete This Item. Items Are issued in this category");
        }
        //throw $this->exception("hello".$sum.$total.$issue.$q);return;
        
    }
}
