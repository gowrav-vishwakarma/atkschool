<?php
class Model_ExamClassSubjectMapAll extends Model_Table {
	var $table= "examsub_map";
	function init(){
		parent::init();
		$this->hasOne('ExamClassMap','exammap_id');
		$this->hasOne('Subject','subject_id');
		$this->hasOne('Session','session_id');

		$this->addField('min_marks')->display(array('grid'=>'grid/inline'));
		$this->addField('max_marks')->display(array('grid'=>'grid/inline'));
		// $this->addField('in_ms_row')->caption('Marksheet Row')->display(array('grid'=>'grid/inline'));

		// $this->hasOne('MS_Sections','marksheet_section_id')->caption('Marksheet Section')->display(array('grid'=>'grid/inline'));
		$this->hasMany('Students_Marks','examsub_map_id');

		$this->addHook('beforeDelete',$this);

	}

	function beforeDelete(){
		if($this->ref('Students_Marks')->sum('marks')->getOne() > 0)
			throw $this->exception(' You can Not remove It contains Marks, Remove Marks First');
	}

	function createNew($subject,$exammap,$session=null){
		if(!$session) $session=$this->add('Model_Sessions_Current')->tryLoadAny();

		$this['exammap_id']=$exammap->id;
		$this['subject_id']=$subject->id;
		$this['session_id']=$session->id;

		$this->save();
		return $this;
	}


	// function beforeDelete(){
	// 	if($this->ref('Marks')->count()->getOne())
	// 		throw $this->exception('You can not remove, It Contain Marks Record');
	// }

	function promote($from_session, $to_session){

		$old_mapping = $this->add('Model_ExamClassSubjectMapAll');
		$old_mapping->addCondition('session_id',$from_session);

		foreach($old_mapping as $old){
			$new=$this->add('Model_ExamClassSubjectMapAll');
			
			$old_exammap= $this->add('Model_ExamClassMapAll')->load($old['exammap_id']);

			$new_exammap = $this->add('Model_ExamClassMapAll');
			$new_exammap->addCondition('exam_id',$old_exammap['exam_id']);
			$new_exammap->addCondition('class_id',$old_exammap['class_id']);
			$new_exammap->addCondition('session_id',$to_session);
			$new_exammap->loadAny();

			$new['exammap_id'] = $new_exammap->id;
			$new['subject_id']= $old['subject_id'];
			$new['session_id'] = $to_session;

			$new->save();
			
			$new->destroy();
			$old_exammap->destroy();
			$new_exammap->destroy();

		}

	}

	function isAvailable($subject,$exammap,$session=null){

			if(!$session) $session=$this->add('Model_Sessions_Current')->tryLoadAny();
			$exa_class_sub_map=$this->add('Model_ExamClassSubjectMapAll');
			$exa_class_sub_map->addCondition('exammap_id',$exammap->id);
			$exa_class_sub_map->addCondition('subject_id',$subject->id);
			$exa_class_sub_map->addCondition('session_id',$session->id);
			$exa_class_sub_map->tryLoadAny();

			if($exa_class_sub_map->loaded())
				return $exa_class_sub_map;
			else
				return false;


		}
}