<?php

class Model_SubjectClassMapAll extends Model_Table{
	var $table="subject_class_map";
	function init() {
		parent::init();

		$this->hasOne( 'Class', 'class_id' );
		$this->hasOne( 'Subject', 'subject_id' );
		$this->hasOne( 'Session', 'session_id' );

	}

	function promote( $from_session, $to_session ) {

		$old_mapping=$this->add( 'Model_SubjectClassMapAll' );
		$old_mapping->addCondition( 'session_id', $from_session );

		foreach ( $old_mapping as $old ) {

			$new=$this->add( 'Model_SubjectClassMapAll' );
			$new['class_id']=$old['class_id'];
			$new['subject_id']=$old['subject_id'];
			$new['session_id']=$to_session;
			$new->save();
			$new->destroy();
		}

		

	}

	function createNew($class,$subject,$session){
		$this['class_id']=$class->id;
		$this['subject_id']=$subject->id;
		$this['session_id']=$session->id;
		$this->save();
		return $this;
	}

}
