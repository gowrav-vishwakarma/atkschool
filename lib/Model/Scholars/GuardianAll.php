<?php

class Model_Scholars_GuardianAll extends Model_Table{
	var $table="scholar_guardian";

	function init(){
		parent::init();

		$this->hasOne('Scholar','scholar_id');
        $this->addField('gname')->caption('Guardian')->display('hindi');
        // $this->addExpression('Guardian')->set('gname');
        $this->add("filestore/Field_Image","image");
        $this->addField('relation')->display('hindi');
        $this->addField('contact');
        $this->addField('is_active')->type('boolean')->defaultValue(true);
        $this->addField('address')->display('hindi');   
        $this->hasMany('Students_Movement','scholar_id');

        $fs=$this->leftJoin('filestore_file','image')
                        ->leftJoin('filestore_image.original_file_id')
                        ->leftJoin('filestore_file','thumb_file_id');
        $fs->addField('image_url','filename')->display(array('grid'=>'picture'));//->system(true);
        
        $this->addExpression('name')->set('gname');

    }
}