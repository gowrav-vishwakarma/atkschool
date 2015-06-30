<?php
class page_hostel_hostelallotement extends Page{
	function page_index(){
		$acl=$this->add('xavoc_acl/Acl');
		$this->api->stickyGET('filter');
		$this->api->stickyGET('class');
		$form=$this->add('Form');
		$form->addField('dropdown','class')->setEmptyText('----')->setAttr('class','hindi')->setModel('Class');
		$form->addSubmit('Get List');
		
		$grid=$this->add('Grid');
		$s=$this->add('Model_StudentsAndHostelers');
		if($_GET['filter']){
			$s->addCondition('class_id',$_GET['class']);
		}else{
			$s->addCondition('class_id',-1);
		}

		$s->_dsql()->del('order')->order('fname','asc');
		$grid->setModel($s,array('name','fname','father_name', 'isScholared','isalloted','building_name','room_no'));
		$grid->addColumn('expander','alott','Allotement');
		$grid->addColumn('expander','deallot','De Allotement');
		$grid->addClass('reloadable');
		$grid->js('reloadme',$grid->js()->reload());
		$grid->addFormatter('father_name','hindi');


		$grid->addPaginator();

		if($form->isSubmitted()){
			$grid->js()->reload(array(
									"class"=>$form->get('class'),
									"filter"=>-1
								))->execute();
		}
	}

	
	function page_alott(){

		$this->api->stickyGET('student_id');


		$form=$this->add('Form');
		$hdrp=$form->addField('dropdown','hostel')->setEmptyText('------')->setNotNull();
		$hdrp->setModel('Hostel');

		$rdrp=$form->addField('dropdown','room_no');

		$r=$this->add('Model_HostelRoom');
		if($_GET['hostel_idx']){
			$r->addCondition('hostel_id',$_GET['hostel_idx']);
			// throw $this->exception("hi");
		}

		$rdrp->setModel($r);

		$hdrp->js('change',$form->js()->atk4_form('reloadField','room_no',array($this->api->getDestinationURL(),'hostel_idx'=>$hdrp->js()->val())));      

		$form->addSubmit('Allot');

		if($form->isSubmitted()){

			try{
				$check_room=$this->add('Model_HostelRoom');
				$check_room->load($form->get('room_no'));
				if($check_room['capacity'] <= $check_room['alloted'])
					throw $this->exception('Room is already full. no capicity available');

				$ra=$this->add('Model_RoomAllotement');
				$ra['student_id']=$_GET['student_id'];
				$ra['room_id']=$form->get('room_no');
				$ra->save();
			}catch(Exception $e){
				$form->js()->univ()->errorMessage($e->getMessage())->execute();
			}
				$room=$ra->ref('room_id');
				$remain=$room->get("capacity") - $room->get('alloted');
				$form->js(null,array(
							$form->js()->univ()->successMessage("Room alloted now ". $remain ." seats remaining in the room"),
							// TODO reload the page
							$form->js()->_selector('.reloadable')->trigger('reloadme')
						)
				)->univ()->closeExpander()->execute();
		}

		
	}
		function page_deallot(){

			$this->api->stickyGET('student_id');
			$h=$this->add('Model_Hosteler');
			$h->tryLoad($_GET['student_id']);

			if(!$h->loaded()){
				$this->add('View_Info')->set('This is Not a Hostler');
				return;
			}
			$form=$this->add('Form');
			$form->add('View_Error')->set('Are you Sure?');
			$form->addSubmit('Delete');

			if($form->isSubmitted()){

				// $s=$this->add('Model_Hosteler');
				// $s->tryLoad($_GET['student_id']);
				// if($s['attendance_status'] == 'inward')
				// 	throw $this->exception('Cannot DEAllot room to an already inward student. kindly make him/her outward first.');

				$ra=$this->add('Model_RoomAllotement');

				$ra->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
				$ra->addCondition('student_id',$_GET['student_id']);
				$ra->tryLoadAny();
				$ra->delete();
				$form->js(null,$form->js()->_selector('.reloadable')->trigger('reloadme'))->univ()->closeExpander()->execute();
			}

		}
}

