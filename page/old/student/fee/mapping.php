<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class page_student_fee_mapping extends Page {
    function init(){
        parent::init();
        $cols=$this->add('Columns');
        $left=$cols->addColumn();
        $right=$cols->addColumn();
        $student_crud=$left->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
        $student_crud->setModel('Student_Current');
        if($student_crud->grid){
            $student_crud->grid->addColumn('button','grid_student_id');
        }
        if($_GET['grid_student_id']){
            $right->js()->reload(array('student_id'=>$_GET['grid_student_id']))->execute();
        }
        if($_GET['student_id']){
            $c=$right->add('CRUD');
            $m=$this->add('Model_Fee_Applicable');
            $m->addCondition('student_id',$_GET['student_id']);
            $c->setModel($m);
//            $this->js()->univ()->successMessage('got '.$_GET['student_id'])->execute();
        }
    }
}