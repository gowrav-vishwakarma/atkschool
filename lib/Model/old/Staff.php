<?php

class Model_Staff extends Model_Table{
    var $table='staff_master';
    function init(){
        parent::init();
        
        $this->addField('doj')->type('date')->mandatory("Required Field")->defaultValue(date('d-m-Y'))->caption('Date of Joining');
        $this->addField('ename')->mandatory("Name is Must")->caption('Name(English)');
        $this->addField('hname')->mandatory("Name is Must")->caption('Name(Hindi)');
        $this->addField('designation')->mandatory("Designation is Must")->caption('Designation');
        $this->addField('father_name')->mandatory("Required Field")->caption('Father`s Name/Husband`s Name');;
        $this->addField('mother_name')->mandatory("Required Field")->caption('Mother`s Name');
        $this->addField('guardian_name')->caption('Eligiblity');
        $this->addField('dob')->type('date')->mandatory("Required Field")->caption('Date of Birth');
        $this->addField('contact')->mandatory("Required Field");
        $this->add("filestore/Field_Image","image");//->type('image');//$this->add("filestore/Field_Image", "student_image"); 
        $this->addField('address')->datatype('Text')->mandatory("Required Field")->caption('Address');
        $this->addField('sex')->type('radio')->listData(array('Male','Female'))->defaultValue('M');
        $this->addField('accno')->caption('Bank Account Number');
        $this->addField('insurance_no')->caption('Insurance');
        $this->addField('ofhostel')->type('boolean')->caption("Hostel");
        $this->addExpression('name')->set('hname');
        
    }
}