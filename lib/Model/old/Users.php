<?php

class Model_Users extends Model_Table
{
    var $table='users';
    function init()
    {
        parent::init();
        
        $this->addField('username');
        $this->addField('password');
       //=================================master menu================ 
        $this->addField('master')->type('boolean')->caption('MASTER---------------------------MENU-------------------------');
        $this->addField('m_school')->type('boolean')->caption('Master_school');
        $this->addField('m_hostel')->type('boolean')->caption('master_hostel');
        $this->addField('m_s_session')->type('boolean')->caption('Session Master ');
        $this->addField('m_s_class')->type('boolean')->caption('master_school_class');
        $this->addField('m_s_fee')->type('boolean')->caption('master_school_fee');
        $this->addField('m_s_c_cmaster')->type('boolean')->caption('Class master');
        $this->addField('m_s_c_smaster')->type('boolean')->caption('Subject Master');
        $this->addField('m_s_f_fhead')->type('boolean')->caption('Fee Header');
        $this->addField('m_s_f_fee')->type('boolean')->caption('Fee');
        $this->addField('d_s_add')->type('boolean')->caption('Staff Master');
       //===================data menu================================= 
        
        $this->addField('data')->type('boolean')->caption('DATA-------------------------------MENU--------------------------');
        $this->addField('d_school')->type('boolean')->caption('data_school');
        $this->addField('d_hostel')->type('boolean')->caption('data_hostel');
        $this->addField('d_staff')->type('boolean')->caption('data_staff');
        $this->addField('d_h_allot')->type('boolean')->caption('Hostel Allotment');
        $this->addField('d_h_alloted')->type('boolean')->caption('Alloted Student');
        $this->addField('d_h_guardian')->type('boolean')->caption('Guardian');
        $this->addField('d_h_io')->type('boolean')->caption('Student Inward/Outward');
        $this->addField('d_h_report')->type('boolean')->caption('Student Outward Report');
        $this->addField('d_h_disease')->type('boolean')->caption('Disease');
        $this->addField('d_s_io')->type('boolean')->caption('staff Inward/Outward');
        $this->addField('d_s_report')->type('boolean')->caption('staff Outwrad Report');
        $this->addField('d_s_attendence')->type('boolean')->caption('Staff attendence');
       //==========================report menu=====================================
        $this->addField('reports')->type('boolean')->caption('REPORT--------------------MENU-----------------------');
         $this->addField('r_school')->type('boolean')->caption('report_school');
        $this->addField('r_hostel')->type('boolean')->caption('report_hostel');
        $this->addField('r_h_attendence')->type('boolean')->caption('student attendence');
       //=================================user menu================================= 
        
        $this->addField('user')->type('boolean')->caption('User-menu')->system(true);
       $this->addField('u_create')->type('boolean')->caption('Create User')->system(true);
        
        
        
        
        
        
        
        
    }
}