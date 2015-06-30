<?php
class page_corrections extends Page {
	function init(){
		parent::init();

		// Create a few new tables
		$this->query("DROP TABLE IF EXISTS `diseases`;");
		$this->query("
				CREATE TABLE `diseases` (
					`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
					`name` varchar( 50 ) NOT NULL ,
					PRIMARY KEY ( `id` )
					) ENGINE = InnoDB DEFAULT CHARSET = latin1;
			");

		$this->query("DROP TABLE IF EXISTS `item_category`;");

		$this->query("
					CREATE TABLE `item_category` (
					`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
					`name` varchar( 100 ) NOT NULL ,
					PRIMARY KEY ( `id` )
					) ENGINE = InnoDB DEFAULT CHARSET = latin1;
			");

		$this->query("ALTER TABLE `item_master` ADD `category_id` INT NOT NULL ");
		$this->query("ALTER TABLE `fee_applicable` CHANGE `fee_id` `fee_class_mapping_id` INT( 11 ) NOT NULL ");
		$this->query("ALTER TABLE `fee_deposit_master` CHANGE `fee_id` `fee_applicable_id` INT( 11 ) NOT NULL ");
		$this->query("ALTER TABLE `users`
  DROP `master`,
  DROP `data`,
  DROP `reports`,
  DROP `user`,
  DROP `m_school`,
  DROP `m_hostel`,
  DROP `m_s_session`,
  DROP `m_s_class`,
  DROP `m_s_fee`,
  DROP `m_s_c_cmaster`,
  DROP `m_s_c_smaster`,
  DROP `m_s_f_fhead`,
  DROP `m_s_f_fee`,
  DROP `d_school`,
  DROP `d_hostel`,
  DROP `d_staff`,
  DROP `d_h_allot`,
  DROP `d_h_alloted`,
  DROP `d_h_guardian`,
  DROP `d_h_io`,
  DROP `d_h_report`,
  DROP `d_h_disease`,
  DROP `d_s_add`,
  DROP `d_s_io`,
  DROP `d_s_report`,
  DROP `d_s_attendence`,
  DROP `r_school`,
  DROP `r_hostel`,
  DROP `r_h_attendence`,
  DROP `r_h_a_total`,
  DROP `r_h_a_class`,
  DROP `u_create`,
  DROP `u_changepaswd`;
");

		$this->query("ALTER TABLE `users` ADD `is_system_admin` TINYINT NOT NULL DEFAULT '0'");
		$this->query('UPDATE users SET is_system_admin=1 WHERE id=1');
		// disease_master scholar_id to student_id and all id conversions
		// Change all Scholars ID to Student ID
		$this->query("ALTER TABLE `disease_master` CHANGE `scholar_id` `student_id` INT( 11 ) NOT NULL ");
		$this->query("ALTER TABLE `disease_master` ADD `disease_id` INT NOT NULL");
		$this->query("ALTER TABLE `item_issue` ADD `receipt_no` INT NOT NULL DEFAULT '0'");
		// $with_scholar = $this->add('Model_Students_Disease');
		// foreach($with_scholar as $dis_tab){
		// 	$s=$this->add('Model_Student');
		// 	$s->addCondition('scholar_id',$dis_tab['student_id']);
		// 	$s->tryLoadAny();
		// 	$with_scholar['student_id'] = $s->id;
		// 	$with_scholar->save();
		// }

		$this->query('ALTER TABLE `bill_master` CHANGE `item_date` `inward_date` DATE NULL DEFAULT NULL ');
		$this->query('ALTER TABLE `bill_master` ADD `session_id` INT NOT NULL ');
		$this->query('ALTER TABLE `item_inward` DROP `session_id`');
		$this->query('UPDATE bill_master SET session_id=8');

		$this->query('ALTER TABLE `student` ADD `is_present` INT NOT NULL DEFAULT 0');

		$this->query('ALTER TABLE `item_master` ADD `stock` INT NOT NULL ');


		$this->changeScholarToStudent('disease_master','Model_Students_Disease','student_id');

		$this->query("ALTER TABLE `hosteller_outward` CHANGE `withid` `gaurdian_id` INT( 11 ) NULL DEFAULT NULL ");
		$this->query('ALTER TABLE `hosteller_outward` DROP FOREIGN KEY `hosteller_outward_ibfk_1` ;');
		$this->query('ALTER TABLE hosteller_outward DROP INDEX scholar_id');
		$this->query('ALTER TABLE `hosteller_outward` CHANGE `scholar_id` `student_id` INT( 11 ) NOT NULL');
		$this->query('UPDATE hosteller_outward SET direction=0');
		$this->changeScholarToStudent('hosteller_outward','Model_Students_Movement','student_id','direction');

		$hm=$this->add('Model_Hosteler');
		foreach($hm as $junk){
			if($hm['attendance_status'] == 'inward'){
				$hm['is_present']=true;
				$hm->save();
			}
		}

		// TODO :: Change Scholar_id to student_id


		// DONET NEED TO USE THIS CORRECTION. OLD DATA WAS CORRECT
		// $m=$this->add('Model_ExamClassSubjectMap');
		// $t=$this->add('Model_SubjectClassMap');
		// foreach($m as $junk){
		// 	$t->load($m['subject_id']);
		// 	$m['subject_id']=$t['subject_id'];
		// 	$m->save();
		// 	$t->unload();
		// }
 

	}

	function query($q) {
        $this->api->db->dsql()->expr($q)->execute();
    }


    // function changeScholarToStudent($table,$model,$field,$inform_field=false){
    // 	$this->add('H2')->set($model);
    // 	$m=$this->add($model);
    // 	// $m->_dsql()->limit(10)->order('id','asc');
    // 	foreach($m as $junk){
    // 		$s=$this->add('Model_Student');
    // 		$s->addCondition('scholar_id',$m[$field]);
    // 		$s->tryLoadAny();
    // 		$this->add('Text')->set('doing '. $m->id . ' from ' . $m[$field] . ' to ' . $s->id);
    // 		$m[$field]=$s->id;
    // 		if($inform_field) $m[$inform_field]=1;
    // 		$m->save();
    // 	}

    // }

    function changeScholarToStudent($table,$model,$field,$inform_field=false){
    	
    	$q="
    		UPDATE 
    			$table target 
    			join scholars_master sm on target.$field = sm.id 
    			join student st on st.scholar_id=sm.id

    			SET 
    			target.$field = st.id
    	";

    	$this->query($q);

    }
}