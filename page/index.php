<?php
class page_index extends Page {

    
    function init(){
        parent::init();
        
        $page=$this;
        $this->add('H1')->set('Bal Vinay Mandir Senior Secondary School,Udaipur');
        $this->add('H5')->set('J-11,Haridas Ji Ki Magri,Udaipur');
        $news=$this->api->db->dsql()->expr("select name from news where id=1")->do_getOne();
        $this->add('Html')->set('<FONT SIZE=\"4\" FACE=\"courier\" COLOR=blue><MARQUEE WIDTH=100%  BGColor=yellow onmouseover="this.stop();" onmouseout="this.start();" class="hindi" >'.$news.'</MARQUEE></FONT>');
      

       $this->add('HTML')->set('<center><img src="welcome.jpg" alt="Plase put a file named welcome.jpg in your d:\xampp\htdocs\soft folder"/></center>'); 
        
//       $Birthdate=$this->api->db->dsql()->expr("select dob from scholars_master where id =15 ")->getOne();
//       echo $this->GetAge($Birthdate);
        
    }
//    function GetAge($Birthdate)
//   {
//        // Explode the date into meaningful variables
//        list($BirthYear,$BirthMonth,$BirthDay) = explode("-", $Birthdate);
//        // Find the differences
//        $YearDiff = date("Y") - $BirthYear;
//        $MonthDiff = date("m") - $BirthMonth;
//        $DayDiff = date("d") - $BirthDay;
//        // If the birthday has not occured this year
//        if ($DayDiff < 0 || $MonthDiff < 0)
//          $YearDiff--;
//        return $YearDiff;
//}

}
