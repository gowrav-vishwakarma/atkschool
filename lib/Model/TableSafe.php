<?php

class Model_TableSafe extends Model_Table{
	function init(){
		parent::init();
		$this->addHook('beforeDelete',array($this,'checkMultiCounts'));
	}

	function checkMultiCounts(){
		foreach($this->elements as $field){
			if($field instanceof SQL_Many){
				$rel = str_replace('Model_', '', $field->model_name);
				if($this->ref($rel)->count()->getOne() > 0 ) throw $this->exception('Cannot Delete');
			}
		}
	}
}