<?php
/**
 * /tmp/phptidy-sublime-buffer.php
 *
 * @package default
 */


class page_student_marks extends Page{


	/**
	 *
	 *
	 * @return unknown
	 */
	function page_index() {
		// parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form', null, null, array('form_horizontal'));
		$class_field=$form->addField('dropdown', 'class')->setEmptyText('----')->setAttr('class', 'hindi')->setNotNull();
		$class_field->setModel('Class');

		$ecm=$this->add('Model_ExamClassMap');

		/**
		 *
		 */
		$ecm->addExpression('name')->set(function($m, $q) {
				return $m->refSQL('exam_id')->fieldQuery('name');
			});


		if ($_GET['class_filter']) {
			$ecm->addCondition('class_id', $_GET['class_filter']);
		}

		$exam_field=$form->addField('dropdown', 'exammap')->setEmptyText('----')->setAttr('class', 'hindi')->setNotNull();

		$exam_field->setModel($ecm);


		$class_field->js('change', $form->js()->atk4_form('reloadField', 'exammap', array($this->api->url(), 'class_filter'=>$class_field->js()->val())));

		$sub_field=$form->addField('dropdown', 'examsubjectmap','Subject')->setEmptyText('----')->setAttr('class', 'hindi')->setNotNull();

		$ecsm=$this->add('Model_ExamClassSubjectMap');

		/**
		 *
		 */
		$ecsm->addExpression('name')->set(function($m, $q) {
				return $m->refSQL('subject_id')->fieldQuery('name');
			});
		if ($_GET['exam_filter']) {
			$ecsm->addCondition('exammap_id', $_GET['exam_filter']);
		}


		$sub_field->setModel($ecsm);

		$exam_field->js('change', $form->js()->atk4_form('reloadField', 'examsubjectmap',
				array($this->api->url(), 'exam_filter'=>$exam_field->js()->val())));

		$form->addSubmit('GetList');

		// removed from here
		if ($form->isSubmitted()) {
			$examclassmap=$this->add('Model_ExamClassMap');
			$examclassmap->tryLoad($form->get('exammap'));
			if (!$examclassmap->loaded()) throw $this->exception("Error Processing Request in page student/marks at line number " . __FILE__ . " at " . __LINE__."class_id ". $form->get('class') ."exam_id" . $form->get('exam'));

			$examclassubjectmap=$this->add('Model_ExamClassSubjectMap');
			$examclassubjectmap->tryLoad($form->get('examsubjectmap'));
			if (!$examclassubjectmap->loaded()) throw $this->exception("Error Processing Request in page student/marks at line number 57 ");

			$student_marks=$this->add('Model_Students_Marks');
			$student_join_marks=$student_marks->join('student.id', 'student_id');
			$student_join_marks->addField('class_id');
			$student_join_marks->addField('session_id');
			$student_marks->addCondition('class_id', $form->get('class'));
			$student_marks->addCondition('examsub_map_id', $examclassubjectmap->id);
			$student_marks->addCondition('session_id', $this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));

			$students_in_marks_table_for_this_class= $student_marks->count()->getOne();

			
			$class=$this->add('Model_Class');
			$class->load($form->get('class'));
			$total_students_in_class=$class->ref('Students_Current')->count()->getOne();

// throw new Exception($students_in_marks_table_for_this_class."  ".$total_students_in_class, 1);


			if ($total_students_in_class != $students_in_marks_table_for_this_class) {
				$new_students=array();
				foreach ($student=$class->ref('Students_Current') as $junk) {
					$student_marks1=$this->add('Model_Students_Marks');
					$student_marks1->addCondition('student_id', $student->id);
					// $student_marks->debug();
					$student_marks1->addCondition('examsub_map_id', $examclassubjectmap->id);
					$student_marks1->tryLoadAny();
					if (!$student_marks1->loaded()) {
						$new_students[] = $student->id;
						// throw $this->exception("Error Processing Request ".$student->id." ".$students_in_marks_table_for_this_class);
						$student_marks1['student_id'] =$student->id;
						$student_marks1['examsub_map_id'] =$examclassubjectmap->id;
						$student_marks1->save();
					}
				}
			}

			// print_r($new_students);

			$form->js()->univ()->newWindow($this->api->url("./marksinput",
					array('class'=>$form->get('class'), 'subject'=>$form->get('examsubjectmap'),'exammap'=>$form->get('exammap')))
				, null, 'height=689,width=1246,scrollbar=1')->execute();
		}
	}


	/**
	 *
	 */
	function page_marksinput() {

		$this->api->stickyGET('class');
		$this->api->stickyGET('subject');
		$this->api->stickyGET('exammap');

		$subject=$this->add('Model_ExamClassSubjectMap');
		$subject->load($_GET['subject']);
		$class=$this->add('Model_Class');
		$class->load($_GET['class']);

		$ecm=$this->add('Model_ExamClassMapAll');
		$ecm->load($_GET['exammap']);

		$this->add('View_Info')->setHTML('Class:- '." <span class='hindi'>".$class['class_name']."   "."</span>  "."Subject:- "." <span class='hindi'>". $subject->ref('subject_id')->get('name')."</span>" . " Exam:- "." <span class='hindi'>". $ecm['name']."</span>");

		$grid=$this->add('Grid');
		$sm=$this->add('Model_Students_Marks');
		// $sm->table_alias='sm1';
		$smj=$sm->leftJoin('student.id', 'student_id', null, 'sm2');
		$smj->addField('class_id');
		// $smj->addField('subject_id');
		$smj->addField('roll_no');
		// $sm->debug();

		if ($_GET['class']) $sm->addCondition('class_id', $_GET['class']);
		if ($_GET['subject']) $sm->addCondition('examsub_map_id', $_GET['subject']);

		// if ($sm->count()->getOne() == 0) {
		// 	$sm_new=$this->add('Model_Students_Marks');
		// 	$cm=$this->add('Model_Class');
		// 	$cm->load($_GET['class']);
		// 	$s=$cm->ref('Students_Current');
		// 	foreach ($s as $junk) {
		// 		// $sm_new->unload();
		// 		$sm_new['student_id']=$s->id;
		// 		$sm_new['examsub_map_id']=$_GET['subject'];
		// 		// $sm_new->saveAndUnload();
		// 	}

		// }
		// else{
		//  $sm->addCondition('class',-1);
		// }

		$sm->_dsql()->order('roll_no', 'asc');
		$grid->setModel($sm, array('roll_no', 'student', 'max_marks', 'marks'));
		// $grid->dq->order('roll_no','asc');
		$grid->addFormatter('student', 'hindi');
		// $grid->addFormatter('class', 'hindi');
		// $grid->addFormatter('subject', 'hindi');
		$grid->addFormatter('marks', 'grid/inline');
	}


}
