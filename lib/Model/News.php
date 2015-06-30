<?php
class Model_News extends Model_Table{
	var $table="news";
	function init(){
		parent::init();
		$this->addField('name')->type('text')->caption("News");
	}
}