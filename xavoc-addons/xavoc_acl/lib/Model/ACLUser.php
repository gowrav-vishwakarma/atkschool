<?php
namespace xavoc_acl;

class Model_ACLUser extends \Model_Table {
	var $table= 'users';

	function init(){
		parent::init();
		$this->addField('username');
		$this->addField('password');
		$this->add("filestore/Field_Image","user_image_id")->type('image');
		$fs=$this->leftJoin('filestore_file','user_image_id')
                        ->leftJoin('filestore_image.original_file_id')
                        ->leftJoin('filestore_file','thumb_file_id');
        $fs->addField('image_url','filename')->display(array('grid'=>'picture'));
        $fs->addField('original_url','original_filename')->display(array('grid'=>'picture'));
        
		$this->addField('is_system_admin')->type('boolean')->defaultValue(false)->system(true);
		$this->hasMany('xavoc_acl/Acl','acl_user_id');

		$this->addExpression('name')->set('username');
		
		$this->addHook('beforeDelete',$this);
		$this->add('dynamic_model/Controller_AutoCreator');
	}

		function beforeDelete(){
		if($this['is_system_admin']) throw $this->exception('You cannot delete System Administrator');
	}
}