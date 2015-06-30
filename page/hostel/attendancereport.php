<?php 
class page_hostel_attendancereport extends Page{
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		
		$this->api->stickyGET('filter');
		$form=$this->add('Form',null,null,array('form_horizontal'));
		$class_att=$form->addField('dropdown','class_name')->setEmptyText('---')->setAttr('class','hindi');
		$class_att->setModel('Class');
		$hostel_att=$form->addField('dropdown','building_name')->setEmptyText('---');
		$hostel_att->setModel('Hostel');
		$room_att=$form->addField('dropdown','room_no')->setEmptyText('---');
		$form->addField('checkbox','student_vise')->set(true);
		$attendance=$form->addField('dropdown','attendance_status')->setValueList(array("-1"=>"All",
																						"1"=>"Present",
																						"0"=>"Absent"));
		// $room_att->setModel('Model_HostelRoom');

		// $status=$form->addField('dropdown','purpose')->setValueList(array('inward'=>'inward','outward'=>'outward'))->setEmptyText('---');
		$form->addSubmit('Filter');
		if($_GET['filter']){
			$grid=$this->add('Grid');

			$where="";
			$group="";
			$having="";
			$group_by=array();
			if($_GET['filter']){
				if($_GET['class_name']){
					$where.="cm.id= ".$_GET['class_name']. " AND ";
					$group_by[] = "cm.name";	
				} 
				if($_GET['building_name']){
					$where.="h.id=".$_GET['building_name']." AND ";	
					$group_by[] ="h.building_name";
				} 
				if($_GET['room_no']){
					$where.="r.id=".$_GET['room_no']." AND ";
					$group_by[] = "r.room_no";	
				} 
				if($_GET['student_vise']){
					$group_by[] ="s.id";
				}

				if($_GET['attendance_status']!='-1'){
					$having=" having present=".$_GET['attendance_status'];
				}
				// if($_GET['purpose']) $where.="purpose=".$_GET['purpose']." AND";
				if(strlen($where)>0 OR strlen($having)>0) $where = " WHERE s.session_id=".($this->add('Model_Sessions_Current')->tryLoadAny()->get('id'))." AND $where";
				$group=implode(",", $group_by);
				if(count($group_by)>0) $group = " GROUP BY $group";

			}
			$where = trim($where," AND ");
			$q="
				SELECT 
	            	h.building_name building_name, 
	            	r.room_no room_no, 
	            	cm.name class_name, 
	            	sum(is_present) present,
	            	count(s.id) total_students,
	            	sm.fname student_name,
	            	sm.hname student_name_hindi,
	            	sm.father_name
								FROM 
								`student` s 
									join  hostel_allotement hm on s.id=hm.student_id 
									join rooms r on hm.room_id=r.id
									join hostel_master h on h.id=r.hostel_id
									join class_master cm on cm.id=s.class_id
									join scholars_master sm on sm.id=s.scholar_id
									$where
									
									$group
									$having
									order by building_name,room_no

									";
									
			$query = $this->api->db->dsql()->expr($q);

			// $grid->add('Text',null,'quick_search')->set($q);
			

			$grid->addColumn('sno','sno');
			$grid->setSource($query);
			// if(in_array("h.building_name", $group_by)) 
				$grid->addColumn('text','building_name');
			// if(in_array("r.room_no", $group_by)) 
				$grid->addColumn('text','room_no');
			// if(in_array("cm.name", $group_by)) 
				$grid->addColumn('text','class_name');
			if(in_array("s.id", $group_by))
				$grid->addColumn('student_name');
				$grid->addColumn('hindi','student_name_hindi');
				$grid->addColumn('hindi','father_name');
			$grid->addColumn('text','total_students');
			$grid->addColumn('text','present');
			$grid->addFormatter('present','attendance2');
			// $grid->addFormatter('student_name','hindi');
		}else{
			$grid=$this->add('Grid');
			$grid->setSource(array());
		}

		$room_m=$this->add('Model_HostelRoom');
		if($_GET['selected_hostel'])
			$room_m->addCondition('hostel_id',$_GET['selected_hostel']);
		$room_att->setModel($room_m);

		$hostel_att->js('change',$form->js()->atk4_form('reloadField','room_no',array($this->api->url(),'selected_hostel'=>$hostel_att->js()->val())));

		if($form->isSubmitted()){
			// throw $this->exception($form->get('student'));
			$grid->js()->reload(array(
				'class_name'=>$form->get('class_name'),
				'building_name'=>$form->get('building_name'),
				'room_no'=>$form->get('room_no'),
				'student_vise' => $form->get('student_vise'),
				'attendance_status'=>$form->get('attendance_status'),
				'filter'=>1
				))->execute();
		}



	}
}