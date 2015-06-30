<?php

class View_Mapping extends View {
	
	var $leftModel;
	var $mappingModel;
	var $rightModel;
	
	var $leftField;
	var $rightField;

	var $fieldsToShowInGrid;	
	var $deleteFirst;
	var $maintainSession;

	var $allowediting=true;

	var $onlymapped=false;

	var $field_other_then_id=null;

	var $grid;

	function init(){
		parent::init();
		// $this->add('Text')->set($this->rightModel);
		$this->addClass('atk-box ui-widget-content ui-corner-all')
		        ->addStyle('background','#eee');

		$map=$this->leftModel->ref($this->mappingModel);
        $this->grid=$this->add('Grid');
        $this->grid->addClass($this->mappingModel);
        $this->grid->js('reload_me',$this->grid->js()->reload());
        $grid=$this->grid;
        if(is_string($this->rightModel))
	        $rm=$this->add("Model_".$this->rightModel);
	    else
	    	$rm=$this->rightModel;

	    if($this->field_other_then_id)
	    	$rm->id_field=$this->field_other_then_id;
	    
        if(!$this->onlymapped)
			$this->grid->setModel($rm);
		else
			$this->grid->setmodel($map);

		if($this->allowediting){
			$form=$this->add('Form');
			$sel=$form->addField('line','sel');
			$sel->js(true)->closest('.atk-form-row')->hide();
			$sel->set(json_encode($map->dsql()->del('field')->field( $this->rightField )->execute()->stmt->fetchAll(PDO::FETCH_COLUMN)));

			$grid->addSelectable($sel);
			$form->addSubmit('Save');

			if($form->isSubmitted()){
				$this->api->db->beginTransaction();

	            $ids= json_decode($form->get('sel'));
	            // delete old mappings
	            if($this->deleteFirst){
	            	$clone_map= $this->leftModel->ref($this->mappingModel);
	            	foreach($clone_map as $junk){
	            		if($this->field_other_then_id){
				    		if(in_array($clone_map[$this->rightField], $ids))
		            			$clone_map->memorize('keep_added',true);
			    		}else{
			    			if(in_array($clone_map[$this->rightField], $ids))
		            			$clone_map->memorize('keep_added',true); //throw $this->exception('in array '. $clone_map[$this->rightField] . print_r($ids));
			    		}
	            		$clone_map->delete();
	            		$clone_map->forget('keep_added');
	            	}
	            } 


	            $session=$this->add('Model_Sessions_Current')->tryLoadAny()->get('id');
	            $newRow=$this->add('Model_'.$this->mappingModel);

		    	foreach($ids as $id){
		    		$newRow->unload();
		    		if($this->field_other_then_id){
			    		$clone_rm = clone $rm;
			    		$clone_rm->unload()->load($id);
			    		$newRow[$this->rightField] = $clone_rm->get($this->rightField);
		    		}else{
		    			$newRow[$this->rightField] = $id;
		    		}

		    		$newRow[$this->leftField] = $this->leftModel->id;
		    		if($this->maintainSession){
		    			$newRow['session_id'] = $session;
		    		}
		    		$newRow->save();
		    	}

	            $this->api->db->commit();
	            $this->js()->univ()->closeExpander()->successMessage('Mapping saved')->execute();
			}
		}

	}
}