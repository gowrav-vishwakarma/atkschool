<?php

class xTd{
	var $value;
	var $attributes;

	function render(){
		$mycell="<td $this->attributes >$this->value</td>";
		return $mycell;
	}
}

class xTr{
	var $Td = array();
	var $attributes;

	function render(){
		$myrow="<tr $this->attributes>";
		foreach($this->Td as $td){
			$myrow .= $td->render();
		}
		$myrow .= "</tr>";
		return $myrow;
	}
}

class xTable {
	var $Tr = array();
	var $attributes;

	function render(){
		$mytable="
		<style>
			.marksheet td{
				border: 1px solid black;
			}
		</style>
		<table stype='border:1px solid black;' width='100%' class='marksheet' >";
		foreach ($this->Tr as $tr) {
			$mytable .= $tr->render();
		}
		$mytable .= "</table>";
		return $mytable;
	}
}

class View_MS_MainBlock extends View {
	
	var $class;
	var $student;
	var $section;

	var $print_block=true;

	function init(){
		parent::init();

		$class = $this->add('Model_Class')->load($this->class);
		$mark_sheet = $class->ref('MS_Designer')->tryLoadAny();

		$section=$mark_sheet->ref('MS_Sections')->addCondition('id',$this->section)->tryLoadAny();

		$MM_4_Each_Row=$section['max_marks_for_each_subject'];
		$Extra_Totals_Text = $section['extra_totals'];
		$temp = explode(";;", $Extra_Totals_Text);
		$extra_totals_blocks =array();
		$extra_total_after_exam=array();
		$extra_total_title=array();
		$extra_total_exams=array();
		$push_in_last_extra_totals=array();
		$set_show_extra_in_last = array();
		// B1=>A+B=>B=>Two Total;B2=>A+B+C+D+E=>E=>Five Total=>last
		$extra_total_sum_max_marks_afterexam_subject=array();
		$extra_total_sum_marks_afterexam_subject=array();

		$extra_total_sum_max_marks_afterexam = array();
		$extra_total_sum_marks_afterexam = array();
		
		foreach($temp as $t){
			$t=explode("=>", $t);
			$extra_totals_blocks[] = $t[0]; //In which block
			$extra_total_exams[$t[2]][] =$t[1]; // after_exam => what exams code to be added
			$extra_total_after_exam[] = $t[2]; //after which exam
			$extra_total_title[$t[2]][count($extra_total_exams[$t[2]])-1] = $t[3]; //Total title
			if(isset($t[4])){ //Is to be set in last after blocks total
				$push_in_last_extra_totals[$t[2]] = count($extra_total_exams[$t[2]])-1; //No value ref line : 183 approx foreach($exams_texts as $no=>$exams_text){
			}
		}

		// $this->pr($extra_total_title);
		// echo $Extra_Totals_Text;
		/*
			MAIN BLOCK DESIGNING
			FOREACH BLOCKS
				FOREACH EXAMS
					IF First time getting Exams
						array[0][subjects][]=exam_name
					array[BLOCK][EXAM][SUBJECT][Marks]= Marks_Obtained
					array[BLOCK][EXAM][SUBJECT][Max_Marks]= Max_Marks
					IF Block Required Total
						array[BLOCK][EXAM][SUBJECT][TOTAL] += Marks_Obtained
					array[EXAM][SUBJECT][GRAND_TOTAL] += Marks_Obtained
		*/
					
		$block_exam_subject_marks=array();
		$block_exam_sum=array();
		$block_subject_sum=array();
		$subject_sum=array();
		$subject_max_marks_sum =array();
		$block_exam_subject_max_marks=array();
		$block_subject_max_marks_sum=array();
		$block_exam_max_marks_sum=array();
		$block_exam_marks_sum=array();
		$block_max_marks_sum=array();
		$block_marks_sum=array();

		$grand_total_marks=0;
		$grand_total_max_marks=0;

		$subjects=array();
		$exams=array();
		$blocks=array();
		$blocks_exam_count = array();
		$block_exams=array();
		$blocks_exams_subjects=array();
		$blocks_total_fields=array();
		$total_in_blocks=array();
		$blocks_code=array();
		$exams_code=array();

		$examsub_map_id_array=array();

		$failed_subjects=array();

		foreach($subs=$section->ref('MS_SectionSubjects') as $secsub_junk){
			$subjects[] = "<span class='hindismall'>".$subs->ref('subject_id')->get('name')."</span>";
		}

		foreach($block=$section->ref('MS_SectionBlocks') as $block_junk){
			$block_name=$block['name'];
			$blocks[] = $block_name;
			$blocks_code[$block_name] = $block['column_code'];
			$blocks_exam_count[$block_name]= $block->ref('MS_BlockExams')->count()->getOne();
			foreach ($exam=$block->ref('MS_BlockExams') as $exam_junk) {
				$exam_name="<span class='hindismall'>".$exam->ref('exammap_id')->get('name')."</span>";
				$block_exams[$block_name][]=$exam_name;
				if(! in_array($exam_name,$exams)){
						$exams[] = "<span class='hindismall'>".$exam->ref('exammap_id')->get('name')."</span>";
						$exams_code[$exam_name] = $exam['column_code'];
				}
				$exam_subjects=$this->add('Model_ExamClassSubjectMap');
				$exam_subjects->addCondition('exammap_id',$exam['exammap_id']);
				// $exam_subjects->addCondition('marksheet_section_id',$this->section);
				$exam_subjects->_dsql()->order('in_ms_row');
				foreach($exam_subjects as $exam_subject_junk){
					$subject_name = "<span class='hindismall'>".$exam_subjects->ref('subject_id')->get('name')."</span>";
					$blocks_exams_subjects[$block_name][$exam_name][] = $subject_name;
					if(!in_array($subject_name, $subjects)) continue;
					// if(! in_array($subject_name,$subjects)){
					// 	$subjects[] = $subject_name;
					// }
					$examsub_map_id_array[] = $exam_subjects->id;
					$marks=$this->add('Model_Students_Marks');
					$marks->addCondition('student_id',$this->student);
					$marks->addCondition('examsub_map_id',$exam_subjects->id);
					$marks->tryLoadAny();

					$block_exam_subject_marks[$block_name][$exam_name][$subject_name]=$marks['marks'];
					$block_exam_sum[$block_name][$exam_name] +=$marks['marks'];
					$block_subject_sum[$block_name][$subject_name] +=$marks['marks'];
					$subject_sum[$subject_name]+=$marks['marks'];
					$subject_max_marks_sum[$subject_name]+=$marks['max_marks'];
					$block_exam_subject_max_marks[$block_name][$exam_name][$subject_name] = $marks['max_marks'];
					$block_subject_max_marks_sum[$block_name][$subject_name] += $marks['max_marks'];
					$block_exam_max_marks_sum[$block_name][$exam_name] += $marks['max_marks'];
					$block_exam_marks_sum[$block_name][$exam_name] += $marks['marks'];
					$block_max_marks_sum[$block_name] += $marks['max_marks'];
					$block_marks_sum[$block_name] += $marks['marks'];
					$grand_total_marks +=$marks['marks'];
					$grand_total_max_marks +=$marks['max_marks'];
					foreach($extra_total_exams as $after_exam=>$exams_texts){

						foreach($exams_texts as $no=>$exams_text){

							$exams_in_this_extra_total=explode("+", $exams_text);
							if(in_array($exams_code[$exam_name], $exams_in_this_extra_total)){
								$extra_total_sum_max_marks_afterexam_subject [$after_exam][$no][$subject_name] += $marks['max_marks'] ;
								$extra_total_sum_marks_afterexam_subject[$after_exam][$no][$subject_name] += $marks['marks'];

								$extra_total_sum_max_marks_afterexam [$after_exam][$no] += $marks['max_marks'];
								$extra_total_sum_marks_afterexam [$after_exam][$no] += $marks['marks'];
							}
					

						}	
					}
				}
			}
			if($block['is_total_required']){
				$total_in_blocks[] = $block_name;
				$blocks_total_fields[$block_name]=$block['total_title'];
			}
		}

		// GRADE GRACE AND SUPPLIMENTRY DECIDER
		if($section['grade_decider']){
			foreach($subjects as $sub){
				if($subject_sum[$sub] < ($subject_max_marks_sum[$sub] * 36 /100.0)){
					$failed_subjects[$sub]['achieved'] = $subject_sum[$sub];
					$failed_subjects[$sub]['max_marks'] = $subject_max_marks_sum[$sub];
					$failed_subjects[$sub]['diff'] = ($subject_max_marks_sum[$sub] * 36 /100.0) - $subject_sum[$sub];
					$failed_subjects[$sub]['grace_allowed']=array(
															"5_per"=>$subject_max_marks_sum[$sub] * 5/100.0,
															"2_per"=>$subject_max_marks_sum[$sub] * 2/100.0
															);
					$failed_subjects[$sub]['can_suplimentry'] = $subject_sum[$sub] > ($subject_max_marks_sum[$sub] * 25 /100.0);
				}
			}
		}
		$this->api->memorize('failed_subjects',$this->api->recall('failed_subjects',array()) + $failed_subjects );



		// $this->pr($extra_total_sum_max_marks_afterexam);
		// $this->pr($blocks_code);
		// TABLE start
		$table = new xTable();
		
		// TOP ROW SUBJECT and BLOCKS
		$cur_row = $table->Tr[] = new xTr();
		$cur_td = $cur_row->Td[] = new xTd();
		$cur_td->value = 'fo"k;';
		$cur_td->attributes = "rowspan=2 align='center'";
		if($MM_4_Each_Row){
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = PURNANK;
			$cur_td->attributes="rowspan=2 align='center'";
		}
		foreach($blocks as $block_junk){
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = $block_junk;
			$colspan=$blocks_exam_count[$block_junk];
			// echo "colspan without total" . $colspan . "<br/>";
			if(in_array($block_junk, $total_in_blocks)) $colspan++;
			// echo "colspan after total" . $colspan . "<br/>";
			if(in_array($blocks_code[$block_junk], $extra_totals_blocks) and $blocks_code[$block_junk] != null){
				foreach($extra_totals_blocks as $etb){
					// echo $etb .'=='. $blocks_code[$block_junk]. "<br/>";
					if($etb == $blocks_code[$block_junk] ) $colspan += 1;
					// echo "yes eq colspan " . $colspan . "<br/>";
				}
			} 
			$cur_td->attributes = "colspan=" . $colspan . " align='center'";
		}

		if($section['has_grand_total']){
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = SARVYOG;
			$cur_td->attributes="rowspan=2 align='center'";
		}

		if($section['show_grade']){
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = "xzsM";
			$cur_td->attributes="rowspan=2 align='center'";
		}

		// Exam RoW
		$cur_row = $table->Tr[] = new xTr();
		foreach($blocks as $block_junk){
			foreach($block_exams[$block_junk] as $be){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = $be;
				$cur_td->attributes = " align='center'";
				if(in_array($exams_code[$be], $extra_total_after_exam)){
					if(!array_key_exists($exams_code[$be], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
						foreach($extra_total_exams[$exams_code[$be]] as $no=>$exams_texts){
							$cur_td = $cur_row->Td[] = new xTd();
							$cur_td->value = $extra_total_title[$exams_code[$be]][$no];
							$cur_td->attributes = " align='center'";
						}
					}
					else{
						$set_show_extra_in_last[$push_in_last_extra_totals[$exams_code[$be]]][]=$exams_code[$be];
					}
				}
			}
			if(in_array($block_junk, $total_in_blocks)){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = $blocks_total_fields[$block_junk];	
				$cur_td->attributes = " align='center'";
			}
			if(count($set_show_extra_in_last) != 0){ 
				// chk
				foreach($set_show_extra_in_last as $no=>$sseil){
					foreach($sseil as $extra_title_temp){
						$cur_td = $cur_row->Td[] = new xTd();
						$cur_td->value = $extra_total_title[$extra_title_temp][$no];
						$cur_td->attributes = " align='center'";
					}
				}
			}
			$set_show_extra_in_last=array();

		}

		// PURNANK ROW if not for each row
		if(!$MM_4_Each_Row){
			$cur_row = $table->Tr[] = new xTr();
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = PURNANK;
			foreach($blocks as $block_junk){
				foreach($block_exams[$block_junk] as $exam){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $block_exam_subject_max_marks[$block_junk][$exam][$subjects[0]];
					if(in_array($exams_code[$exam], $extra_total_after_exam)){
						if(!array_key_exists($exams_code[$exam], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
							foreach($extra_total_exams[$exams_code[$exam]] as $no=>$exams_texts){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_max_marks_afterexam_subject[$exams_code[$exam]][$no][$subjects[0]];
								$cur_td->attributes = " align='center'";
							}
						}else{
							$set_show_extra_in_last[$push_in_last_extra_totals[$exams_code[$exam]]][]=$exams_code[$exam];
						}
					}
					// if(in_array($exams_code[$exam], $extra_total_after_exam)){
					// 	$cur_td = $cur_row->Td[] = new xTd();
					// 	$cur_td->value = $extra_total_sum_max_marks_afterexam_subject[$exams_code[$exam]][$subjects[0]];
					// 	$cur_td->attributes = " align='center'";
					// }
				}
				// Extra total check TODO

				if(in_array($block_junk, $total_in_blocks)){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $block_subject_max_marks_sum[$block_junk][$subjects[0]];
				}
				if(count($set_show_extra_in_last) != 0){ 
					// chk
					foreach($set_show_extra_in_last as $no=>$sseil){
						foreach($sseil as $extra_temp){
							$cur_td = $cur_row->Td[] = new xTd();
							$cur_td->value = $extra_total_sum_max_marks_afterexam_subject[$extra_temp][$no][$subjects[0]];
							$cur_td->attributes = "style='font-weight:bold' align='center'";
						}
					}
					$set_show_extra_in_last=array();
				}
			}
			
			if($section['has_grand_total']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->attributes="style='font-weight:bold' align='center'";
				$cur_td->value = $subject_max_marks_sum[$subjects[0]];
			}

			if($section['show_grade']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = "-";
				$cur_td->attributes = " align='center'";
			}
		}

		// Each Subjects Row
		foreach($subjects as $sub){
			$cur_row = $table->Tr[] = new xTr();
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = $sub;
			if($MM_4_Each_Row){ //IF each row has max marks to be shown
				$cur_td->attributes = "rowspan=2";
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = PURNANK. "<br>" . PRAPTANK;
				$cur_td->attributes="rowspan=2";
				foreach($blocks as $block_junk){
					foreach($block_exams[$block_junk] as $exam){
						$cur_td = $cur_row->Td[] = new xTd();
						$cur_td->attributes="style='font-weight:bold' align='center'";
						$cur_td->value = $block_exam_subject_max_marks[$block_junk][$exam][$sub];
						if(in_array($exams_code[$exam], $extra_total_after_exam)){
							if(!array_key_exists($exams_code[$exam], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
								foreach($extra_total_exams[$exams_code[$exam]] as $no=>$exams_texts){
									$cur_td = $cur_row->Td[] = new xTd();
									$cur_td->value = $extra_total_sum_max_marks_afterexam_subject[$exams_code[$exam]][$no][$sub];
									$cur_td->attributes = " align='center'";
								}
							}else{
								$set_show_extra_in_last[$push_in_last_extra_totals[$exams_code[$exam]]][]=$exams_code[$exam];
							}
						}
					}
					if(in_array($block_junk, $total_in_blocks)){
						$cur_td = $cur_row->Td[] = new xTd();
						$cur_td->attributes="style='font-weight:bold' align='center'";
						$cur_td->value = $block_subject_max_marks_sum[$block_junk][$sub];
					}		
					if(count($set_show_extra_in_last) != 0){ 
						// chk
						foreach($set_show_extra_in_last as $no=>$sseil){
							foreach($sseil as $extra_temp){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_max_marks_afterexam_subject[$extra_temp][$no][$sub];
								$cur_td->attributes = "style='font-weight:bold' align='center'";
							}
						}
						$set_show_extra_in_last=array();
					}
				}	
				
				if($section['has_grand_total']){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $subject_max_marks_sum[$sub];
				}

				if($section['show_grade']){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->value = "-";
					$cur_td->attributes = " align='center'";
				}

				// Add new row for achieved marks
				$cur_row = $table->Tr[] = new xTr();
			}
			// Add Achived Marks Row
			foreach($blocks as $block_junk){
				foreach($block_exams[$block_junk] as $exam){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->value = $block_exam_subject_marks[$block_junk][$exam][$sub];
					$cur_td->attributes = " align='center'";
					if(in_array($exams_code[$exam], $extra_total_after_exam)){
						if(!array_key_exists($exams_code[$exam], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
							foreach($extra_total_exams[$exams_code[$exam]] as $no=>$exams_texts){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_marks_afterexam_subject[$exams_code[$exam]][$no][$sub];
								$cur_td->attributes = " align='center'";
							}
						}else{
							$set_show_extra_in_last[$push_in_last_extra_totals[$exams_code[$exam]]][]=$exams_code[$exam];
						}
					}
				}
				if(in_array($block_junk, $total_in_blocks)){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->value = $block_subject_sum[$block_junk][$sub];
					$cur_td->attributes = " align='center'";
				}
				if(count($set_show_extra_in_last) != 0){ 
					// chk
					foreach($set_show_extra_in_last as $no=>$sseil){
						foreach($sseil as $extra_temp){
							$cur_td = $cur_row->Td[] = new xTd();
							$cur_td->value = $extra_total_sum_marks_afterexam_subject[$extra_temp][$no][$sub];
							$cur_td->attributes = " align='center'";
						}
					}
					$set_show_extra_in_last=array();
				}
			}
			if($section['has_grand_total']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->attributes="style='font-weight:bold' align='center'";
				$cur_td->value = $subject_sum[$sub];
			}
			if($section['show_grade']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = ($subject_sum[$sub] / $subject_max_marks_sum[$sub] * 100  <= 85)
											? ($subject_sum[$sub] / $subject_max_marks_sum[$sub] * 100  <= 70)
												? ($subject_sum[$sub] / $subject_max_marks_sum[$sub] * 100  < 50)
													? ($subject_sum[$sub] / $subject_max_marks_sum[$sub] * 100  <= 30)
								? 'E' :'D' : 'C': 'B': 'A';
				;
				$cur_td->attributes = " align='center' class='english'";
			}
		}

		// Total in Sections as bottom TR
		if($section['total_at_bottom']){
			$cur_row = $table->Tr[] = new xTr();
			$cur_td = $cur_row->Td[] = new xTd();
			$cur_td->value = TOTAL;

			if($MM_4_Each_Row){
				$cur_td->attributes = "rowspan=2";
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->value = PURNANK."<br>" . PRAPTANK;
				$cur_td->attributes="rowspan=2";
				foreach($blocks as $block_junk){
					foreach($block_exams[$block_junk] as $exam){
						$cur_td = $cur_row->Td[] = new xTd();
						$cur_td->attributes="style='font-weight:bold' align='center'";
						$cur_td->value = $block_exam_max_marks_sum[$block_junk][$exam];
						if(in_array($exams_code[$exam], $extra_total_after_exam)){
							if(!array_key_exists($exams_code[$exam], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
								foreach($extra_total_exams[$exams_code[$exam]] as $no=>$exams_texts){
									$cur_td = $cur_row->Td[] = new xTd();
									$cur_td->value = $extra_total_sum_max_marks_afterexam[$exams_code[$exam]][$no];
									$cur_td->attributes = " align='center'";
								}
							}else{
								$set_show_extra_in_last[][$push_in_last_extra_totals[$exams_code[$exam]]]=$exams_code[$exam];
							}
						}
					}
					if(in_array($block_junk, $total_in_blocks)){
						$cur_td = $cur_row->Td[] = new xTd();
						$cur_td->attributes="style='font-weight:bold' align='center'";
						$cur_td->value = $block_max_marks_sum[$block_junk];
					}

					if(count($set_show_extra_in_last) != 0){
						// chk 
						foreach($set_show_extra_in_last as $sseil){
							foreach($sseil as $no=>$exam_code){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_max_marks_afterexam[$exam_code][$no];
								$cur_td->attributes = "style='font-weight:bold' align='center'";
							}
						}
						$set_show_extra_in_last=array();
					}

				}	

				if($section['has_grand_total']){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $grand_total_max_marks;
				}
				
				if($section['show_grade']){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = "-";	
				}

				$cur_row = $table->Tr[] = new xTr();
			}

			foreach($blocks as $block_junk){
				foreach($block_exams[$block_junk] as $exam){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $block_exam_marks_sum[$block_junk][$exam];
					if(in_array($exams_code[$exam], $extra_total_after_exam)){
						if(!array_key_exists($exams_code[$exam], $push_in_last_extra_totals)){ //NOT SET Last then show here or set to be shown after Total
							foreach($extra_total_exams[$exams_code[$exam]] as $no=>$exams_texts){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_marks_afterexam[$exams_code[$exam]][$no];
								$cur_td->attributes = " align='center'";
							}
						}else{
								$set_show_extra_in_last[][$push_in_last_extra_totals[$exams_code[$exam]]]=$exams_code[$exam];
						}
					}
				}
				if(in_array($block_junk, $total_in_blocks)){
					$cur_td = $cur_row->Td[] = new xTd();
					$cur_td->attributes="style='font-weight:bold' align='center'";
					$cur_td->value = $block_marks_sum[$block_junk];
				}
				if(count($set_show_extra_in_last) != 0){
						// chk 
						foreach($set_show_extra_in_last as $sseil){
							foreach($sseil as $no=>$exam_code){
								$cur_td = $cur_row->Td[] = new xTd();
								$cur_td->value = $extra_total_sum_marks_afterexam[$exam_code][$no];
								$cur_td->attributes = "style='font-weight:bold' align='center'";
							}
						}
						$set_show_extra_in_last=array();
					}
			}

			if($section['has_grand_total']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->attributes="style='font-weight:bold' align='center'";
				$cur_td->value = $grand_total_marks;
			}
			
			if($section['show_grade']){
				$cur_td = $cur_row->Td[] = new xTd();
				$cur_td->attributes="style='font-weight:bold' align='center'";
				$cur_td->value = "-";	
			}

		}
		// $this->pr(json_decode(json_encode($table)));
		if($this->print_block)
			$this->add('Text')->setHtml($table->render());

		$distinction_subjects=array();
		if($section['grade_decider']){
			$this->api->memorize('grand_total_max_marks',$this->api->recall('grand_total_max_marks',0)+$grand_total_max_marks);
			$this->api->memorize('grand_total_marks',$this->api->recall('grand_total_marks',0)+$grand_total_marks);

			foreach($subjects as $sub){
				if($subject_max_marks_sum[$sub] !=0){
					if(($subject_sum[$sub] / $subject_max_marks_sum[$sub] * 100) >= 75)
						$distinction_subjects[] = $sub;
				}
			}

			$this->api->memorize('distinction_subjects',$this->api->recall('distinction_subjects',array())+$distinction_subjects);
			$this->api->memorize('examsub_map_id_array',$this->api->recall('examsub_map_id_array',array())+$examsub_map_id_array);

		}

	}

	function pr($arr){
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}