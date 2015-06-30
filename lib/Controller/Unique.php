<?php

class Controller_Unique extends AbstractController {
	var $unique_fields;

	function init(){
		parent::init();
		if(!($this->owner instanceof Model_Table)) throw $this->exception('Use with Model_Table only');

		$ni = clone $this->owner;
		$msg="";
		foreach($this->unique_fields as $field=>$value){
			if(is_array($value)){
				foreach($value as $f=>$v){
					$ni->addCondition($f,$v);
					$msg .= " $f with $v value,";
				}
			}else{
				$ni->addCondition($field,$value);
				$msg .= "$field with $value ";
			}
			if($this->owner->loaded())
				$ni->addCondition('id','<>',$this->owner->id);
			$ni->tryLoadAny();
			if($ni->loaded()) throw $this->exception("Already Existes with $msg");//->setField($field);
		}

	}
} 