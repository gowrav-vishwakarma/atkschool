<?php

class View_MS_StudentDetails extends View{
	var $student;

	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return array('view/marksheet/top_student_section');
	}
}