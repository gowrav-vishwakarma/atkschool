<?php

class View_MS_WelcomeImage extends View {
	function init(){
		parent::init();
		// $model=$this->add('Model_MS_WelcomeImage');

		
	}
	function setModel($model){
		
		parent::setModel($model);
		// throw new Exception($this->model, 1);
		if(!$this->model->loaded())
			$this->model->tryloadAny();
			// $str='<img src="upload/'.$this->model['image_url'].'"/>';
			// $str= str_replace('thumb_',"", $this->model['image_url']);
			$this->template->Set("welcome_logo",$this->model['image_url']);
	}

	function defaultTemplate(){
		return array('view/welcomeimage');
	}
}