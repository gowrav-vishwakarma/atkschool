<?php

class Model_Student extends Model_Table{
	var $table="student";

	function init(){
		parent::init();

                $this->hasOne('Scholar','scholar_id')->mandatory('Scholar is must');
                $this->hasOne('Class','class_id')->mandatory('Class is Must');
                $this->hasOne('Session','session_id');
		$this->addField('roll_no')->type('int')->caption('roll number');
                $this->addField('ishostler')->type('boolean')->defaultValue(false)->caption("Is Hostler");
                $this->addField('isScholared')->type('boolean');
                $this->addField('Section');
                $this->addField('store_no');
                $this->addField('isalloted')->type('boolean')->defaultValue(false);
                $this->addField('is_result_stop')->type('boolean')->defaultValue(false);
               
                $this->addField('bpl')->type('boolean')->defaultValue(false);
                // $this->addField('result_stopped')->type('boolean')->defaultValue(false);

                $this->hasMany('RoomAllotement','student_id');
                $this->hasMany('Item_Issue','student_id');
                $this->hasMany('Students_Disease','student_id');
                $this->hasMany('Students_Movement','student_id');
                $this->hasMany('Students_Attendance','student_id');
                $this->hasMany('Students_Marks','student_id');
                $this->hasMany('Fees_Applicable','student_id');

                $this->addExpression('name')->set(function ($m,$q){
                        return $m->refSQL('scholar_id')->dsql()->del('field')->field('hname');
                })->display('hindi');

                $this->addExpression('fname')->set(function ($m,$q){
                        return $m->refSQL('scholar_id')->dsql()->del('field')->field('fname');
                })->caption('Name(English)');


                $this->addExpression("father_name")->set(function($m,$q){
                        return $m->refSQL('scholar_id')->fieldQuery('father_name');
                })->display('hindi');


                

                $this->dsql()->order('fname','asc');

              

	}

        function promote($from_session, $to_session, $from_class, $to_class){

                $old_students=$this->add('Model_Student');
                $old_students->addCondition('class_id',$from_class);
                $old_students->addCondition('session_id',$from_session);

                foreach ($old_students as $student) {
                        $new_student=$this->add('Model_Student');
                        $new_student['scholar_id'] = $student['scholar_id'];
                        $new_student['class_id'] = $to_class;
                        $new_student['session_id']= $to_session;
                        $new_student['bpl'] = $student['bpl'];
                        $new_student['ishostler'] = $student['ishostler'];
                        $new_student['isScholared'] = $student['isScholared'];
                        $new_student->save();
                        $new_student->destroy();
                }
                
        }


        function checkRollNo($roll_no,$class){
                $this->addCondition('roll_no',$roll_no);
                $this->addCondition('class_id',$class);
                $this->tryLoadAny();
                if($this->loaded())
                        return true;
                else 
                        return false;
        }    

        function stopResult(){
                $this['is_result_stop']=!$this['is_result_stop'];
                $this->save();
                return true;
        }

}