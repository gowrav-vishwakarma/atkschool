<?php

class Model_Item_Issue extends Model_Table{
	var $table="item_issue";

	function init(){
		parent::init();
		

		$this->hasOne('Hosteler','student_id');
		$this->hasOne('Item','item_id');
		$this->hasOne('Session','session_id');

		$this->addField('quantity')->mandatory('quantity is Must To Select');
		$this->addField('rate')->mandatory('Rate is Must To Select');
		$this->addField('date')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('receipt_no')->system(true);
		$this->addExpression('amount')->set('round(quantity * rate,2)');
		$this->addExpression("month")->set('Month(`date`)')->display('month');
		$this->addExpression('year')->set('Year(`date`)');
		$this->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));


		$this->addHook('beforeSave',$this);
	}
	function beforeSave(){
		if(!$this->loaded()){
			// search for existing recipt number for current month
			$temp=$this->add('Model_Item_Issue');
			$temp->addCondition('student_id',$this['student_id']);
			$temp->addCondition('month',date('m',strtotime($this['date'])));
			$temp->addCondition('year',date('Y',strtotime($this['date'])));
			$temp->tryLoadAny();
			// $temp->debug();
			if($temp->loaded()){
				// Keeping single receipt number for a month for any student
				$this['receipt_no']=$temp['receipt_no'];
			}else{
			// get new recipt number
				$temp=$this->add('Model_Item_issue');
				$r=$temp->dsql()->del('field')->field('max(receipt_no)')->where('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'))->getOne();
				$this['receipt_no']=$r+1;

			}
		}
	}
}