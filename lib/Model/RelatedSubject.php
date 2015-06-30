<?php

class model_RelatedSubject extends Model_Subject{
	function init(){
		parent::init();
		$this->join('subject_class_map.subject_id','id')
		->hasOne('Class','class_id')->display('hindi');
	}
}