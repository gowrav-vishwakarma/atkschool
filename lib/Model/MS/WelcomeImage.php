<?php

class Model_MS_WelcomeImage extends Model_Table{
	public $table="welcome_image";
	function init(){
		parent::init();
		 $this->add('filestore/Field_Image','welcome_image_id')->type('image');

		 $fs=$this->leftJoin('filestore_file','welcome_image_id')
                        ->leftJoin('filestore_image.original_file_id')
                        ->leftJoin('filestore_file','thumb_file_id');
        $fs->addField('image_url','filename')->display(array('grid'=>'picture'));
        $fs->addField('original_url','original_filename')->display(array('grid'=>'picture'));
        
        $this->addField('is_active')->type('boolean')->defaultValue(0);
		 $this->add('dynamic_model/Controller_AutoCreator');
	}
}