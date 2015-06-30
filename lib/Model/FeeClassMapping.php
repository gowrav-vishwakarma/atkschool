<?php

class Model_FeeClassMapping extends Model_Table{
	var $table="fee_class_mapping";

	function init(){
		parent::init();
		$this->hasOne('Fee','fee_id');
		$this->hasOne('Class','class_id');
		$this->hasOne('Session','session_id');

		$this->hasMany('Fees_Applicable','fee_class_mapping_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('fee_id')->fieldQuery('name');
		});

		$this->addExpression('feehead_id')->set(function($m,$q){
			return $m->refSQL('fee_id')->fieldQuery('feehead_id');
		});

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){
		if(!$this->loaded()){
			$fcm=$this->add('Model_FeeClassMapping');
			$fcm->addCondition('fee_id',$this['fee_id']);
			$fcm->addCondition('class_id',$this['class_id']);
			$fcm->addCondition('session_id',$this['session_id']);
			$fcm->tryLoadAny();
			if($fcm->loaded())
				throw $this->exception("This is allready Exists");
			$this->memorize('newEntry',true);
		}
	}

	function afterSave(){
		if($this->recall('newEntry',false)){
			// This is new saved entry not edited
			$c=$this->add('Model_Class');
			$c->load($this['class_id']);
			
			$f=$this->add('Model_Fee');
			$f->load($this['fee_id']);
			
			$fa=$this->add('Model_Fees_Applicable');

			foreach($c->ref('Students_Current') as $junk){
					$fa['fee_class_mapping_id'] = $this->id;
					$fa['student_id'] =$junk['id'];
					$fa['amount']=$f['scholaredamount'];
					$fa['due']= $f['scholaredamount'];
					if(!$junk['is_hostler'] and $f['for_hostler_only']) continue;
					$fa->saveAndUnload();

			}

		
		}
		


	}

	function beforeDelete(){
			$c=$this->add('Model_Class');
			$c->load($this['class_id']);
			$f=$this->add('Model_Fee');
			$f->load($this['fee_id']);
			

			foreach($c->ref('Students_Current') as $junk){
				$fa=$this->add('Model_FeeClassMapping');
				$fa->addCondition('student_id',$junk['id']);
				$fa->addCondition('fee_class_mapping_id',$this->id);
				$fa->tryLoadAny();
				if($fa->loaded()) $fa->delete();	

			}

	}

	function promote($from_session, $to_session){

		$old_mapping=$this->add('Model_FeeClassMapping');
		$old_mapping->addCondition('session_id',$from_session);

		foreach ($old_mapping as $old) {

			$new=$this->add('Model_FeeClassMapping');
			$new['fee_id']=$old['fee_id'];
			$new['class_id']=$old['class_id'];
			$new['session_id'] = $to_session;
			$new->save();
			$new->destroy();
		}
	}

}