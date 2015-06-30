<?php
class page_showresult extends Page {
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		
		$c=$this->add('Model_Class')->load($_GET['class']);
		$student=$this->add('Model_Student');
		$student->addCondition('roll_no',$_GET['roll_no']);
		$student->tryLoadAny();
		// throw new Exception($student['fname']);
		$this->api->memorize('grand_total_max_marks',0);
		$this->api->memorize('grand_total_marks',0);
		$this->api->memorize('distinction_subjects',array());
		$this->api->memorize('examsub_map_id_array',array());
		$this->api->memorize('failed_subjects',array());


		$topStudentView=$this->add('View_MS_StudentDetails',null,'student_panel');
		$topStudentView->setModel($student);
		$first=true;

		$marksheet=$c->ref('MS_Designer')->tryLoadAny();
		// foreach($marksheet as $marksheet_junk){
			foreach($section = $marksheet->ref('MS_Sections') as $section_junk){
				$v=$this->add('View_MS_MainBlock',array('class'=>$_GET['class'],'student'=>$student->id,'section'=>$section->id,'prnt_block'=>true));
				$first=false;
			}
		// }
		
		// Percentage
		$max=$this->api->recall('grand_total_max_marks',0);
		$marks=$this->api->recall('grand_total_marks',0);
		$grace=array();
		$supplimentry=array();

		$failed_subjects=$this->api->recall('failed_subjects',array());
		// print_r($failed_subjects);

		if(count($failed_subjects) ==0){
			$final_result=PASS;
		}
		elseif(count($failed_subjects) > 2){
			// JUST FAIL
			$final_result = FAIL;
		}else{
			// Grace or suplimentory
			if(count($failed_subjects)==1){
				$failed_subjects_i =array_values($failed_subjects);
				if($failed_subjects_i[0]['diff'] <= $failed_subjects_i[0]['grace_allowed']['5_per']){
					$sub=array_keys($failed_subjects);
					$grace[]=array($sub[0]=>$failed_subjects_i[0]['diff']);
					$final_result='lk- mÙkh.kZ'; //grace
				}
				// Or can be suplimentry
				if(count($grace) != 1){
					$grace=array();
					// Check for suplimentry
					if($failed_subjects_i[0]['can_suplimentry'] == '1'){
						foreach($failed_subjects as $subject=>$details){
							if($details['can_suplimentry']){
								$supplimentry[$subject] = $details['diff'];
							}
						}
						$final_result='iwjd'; //supplimentry
					}else{
						// FAILED
						$supplimentry=array();
						$final_result=FAIL;
					}
				}
			}else{
				foreach($failed_subjects as $subject=>$details){
					// Check the two subjects for 2 percent grade range
					if($details['diff'] <= $details['grace_allowed']['2_per']){
						$grace[]=array($subject=>$details['diff']);
						$final_result='lk- mÙkh.kZ'; //Grace
					}
					// BUT IF both are not in range they might be in supplimentry
					if(count($grace) != 2) {
						$grace=array();
						foreach($failed_subjects as $subject=>$details){
							if($details['can_suplimentry']){
								$supplimentry[$subject] = $details['diff'];
							}
						}
						$final_result='iwjd'; //suplimentry
					}
					if(count($supplimentry) != 2){
						// NOW YOU ARE FAILED.. NO BODY CAN HELP YOU MAN.. AB PADHAI CHALU KARDO BUS
						$supplimentry=array();
						$final_result=FAIL;
					}
				}
			}
		}


		if($max != 0)
			$percentage = $marks/$max * 100.00;
		else
			$percentage = 0;

		
		// Result
		if($percentage >= 36 AND $final_result == PASS) 
			$final_result = PASS;
		
		// Division
		if($percentage >=60 AND $final_result == PASS)
			$division="izFke";
		elseif($percentage >=48 AND $final_result == PASS)
			$division="f}rh;";
		elseif($percentage >=36 AND $final_result == PASS)
			$division="r`rh;";
		else
			$division="-";

		// Rank
		$students_ar=$this->add('Model_Students_Current')->addCondition('class_id',$c->id)->_dsql()->del('field')->field('id')->getAll();
		foreach($students_ar as $s){
			$students[]=$s['id'];
		}

		$examsub_map_id_array=$this->api->recall('examsub_map_id_array');

		$all_class_students_marks=$this->add('Model_Students_Marks')
									->addCondition('student_id','in', $students)
									->addCondition('examsub_map_id','in',$examsub_map_id_array)
									->_dsql()
									->del('field')
									->field('student_id')
									->field($this->api->db->dsql()->expr('SUM(marks) marks'))
									->group('student_id')
									->order('marks','desc')
									->getAll()
									;

		// print_r($all_class_students_marks);

		$rank=1;
		foreach ($all_class_students_marks as $sm) {
			if($sm['student_id'] == $_GET['student']) break;
			$rank++;
		}

		// Grace
		// result declare date
		// Distinction
		$distinction = $this->api->recall('distinction_subjects',array());

		$result=array(
			'percentage'=>$percentage,
			'final_result'=>$final_result,
			'division'=>$division
			);

		$show_final_grade = $marksheet['show_grade'];
		$final_grade = $this->add('Model_Grade')->calculate($marks,$max);

		$this->add('View_MS_Result',array('result'=>$result,'distinction'=>$distinction,'rank'=>$rank,'grace' =>$grace,'supplimentry'=>$supplimentry ,'show_grade'=>$show_final_grade,'grade'=>$final_grade),'right_panel');
		$this->api->add('H1',null,'header')->setAttr('align','center')->setHTML('<span class="hindi" style="font-size: 25px">cky fou; efUnj mPp ek/;fed fo|ky; mn;iqj </span>');
		$fv=$this->add('View_MS_Front',null,'marksheet_front');
		
		$fv->setModel($student->ref('scholar_id'));
		$fv->template->trySet('session',$this->add('Model_Sessions_Current')->tryLoadAny()->get('name'));

		$att_model=$this->add('Model_Students_Attendance');
        // $att_model->addCondition('class_id',$form->get('class'));
        // $att_model->addCondition('month',$form->get('month'));
        $att_model->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
        $att_model->addCondition('student_id',$student->id);
		$atv=$fv->add('View_MS_Attendance',null,'attendance_block');
		$atv->setModel($att_model);
		$this->add('Text',null,'declare_date')->set(date('d-M-Y',strtotime($marksheet['declare_date'])));

	}

	function defaultTemplate(){
		return array('view/marksheet/marksheet');
	}

	function render(){
		// $this->api->template->del('header');
		$this->api->template->del('logo');
		$this->api->template->del('Menu');
		$this->api->template->del('date');
		$this->api->template->del('welcome');
		$this->api->template->del('footer_text');

		parent::render();
	}
}