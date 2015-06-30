<?php
class page_reports_hostel_store_recieptR extends Page
{
    function  init()
    {
        parent::init();
         $this->api->stickyGET('month');
         $this->api->stickyGET('store_no');
//============================== Name of school=====================         
         $this->add('Html')->set('
<style type="text/css">
<!--
.style1 {
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
	font-size: 24px;
}
-->
</style>
<body>
<label >
<div align="center" class="style1">Bal Vinay Mandir Senior Secondary School,Udaipur</div>
</label>
</form>
</body>
');
 //=============== student's name and father name==========================
 $name=$this->api->db->dsql()->expr("select scholars_master.hname as `name` from scholars_master,student where student.scholar_id=scholars_master.id and student.store_no=".$_GET['store_no']." and student.session_id in (select id from session_master where session_master.iscurrent=true)")->getOne();        
 $fname=$this->api->db->dsql()->expr("select scholars_master.father_name as `name` from scholars_master,student where student.scholar_id=scholars_master.id and student.store_no=".$_GET['store_no']." and student.session_id in (select id from session_master where session_master.iscurrent=true)")->getOne();        
$class=$this->api->db->dsql()->expr("select CONCAT(`name`,\"-\",class_master.section) as class from class_master ,student where class_master.id=student.class_id AND store_no=".$_GET['store_no'])->getOne();
 $this->add('HTML')->set('
<style type="text/css">
<!--
.style2 {
	font-family: "Kruti Dev 010";
	
	font-size: 24px;
}
-->
</style>
</head>

<body>
<label >
<div align="center" class="style2">
  <p><b>uke</b> & '.$name.'<br>
  <b>firk dk uke</b> & '.$fname.'<br>
      <b>d{kk</b> & '.$class.'
  </p>
</div>
</label>
</body>
</html>
');
         
         
         if($_GET['month']=='13')
        {
            $q=$this->api->db->dsql()->expr("SELECT id, `month`, monthnum, rno, sum(total) AS total FROM ( SELECT DISTINCT item_issue.item_id AS id, MONTHNAME(item_issue.date) AS `month`, MONTH (item_issue.date) AS `monthnum`, ( item_issue.quantity * item_inward.rate ) AS total, reciept.id AS rno FROM item_inward, item_issue, item_master, reciept WHERE item_inward.item_id = item_issue.item_id AND reciept.store_no = ".$_GET['store_no']." AND MONTH (reciept.reciept_month) = MONTH (item_issue.date) AND item_issue.item_id = item_master.id AND item_issue.student_id IN ( SELECT id FROM student WHERE store_no = ".$_GET['store_no']." ) AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )) AS fake GROUP BY `month` ORDER BY monthnum");
            $grid=$this->add('Grid');
             $grid->addColumn('text','rno','Receipt Number');
            $grid->addColumn('text','month');
            $grid->addColumn('text','total','Amount');
            $grid->setSource($q);
            $total=$this->api->db->dsql()->expr("SELECT sum(total) AS total FROM ( SELECT DISTINCT item_issue.item_id AS id, MONTHNAME(item_issue.date) AS `month`, MONTH (item_issue.date) AS `monthnum`, ( item_issue.quantity * item_inward.rate ) AS total, reciept.id AS rno FROM item_inward, item_issue, item_master, reciept WHERE item_inward.item_id = item_issue.item_id AND reciept.store_no = ".$_GET['store_no']." AND MONTH (reciept.reciept_month) = MONTH (item_issue.date) AND item_issue.item_id = item_master.id AND item_issue.student_id IN ( SELECT id FROM student WHERE store_no = ".$_GET['store_no']." ) AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )) AS fake")->getOne();
           $this->add('H5')->set('Total:'.$total);
            }
        else
        {
          $rno=$this->api->db->dsql()->expr("select id from reciept where store_no=".$_GET['store_no']." AND MONTH(reciept_month)=".$_GET['month'])->getOne();  
          $this->add('H5')->set('Receipt No : '.$rno);  
          $q=$this->api->db->dsql()->expr("SELECT id, item, quantity, rate, sum(total) AS total FROM ( SELECT DISTINCT item_issue.item_id AS id, item_master.`name` AS item, item_issue.quantity, item_inward.rate, ( item_issue.quantity * item_inward.rate ) AS total FROM item_inward, item_issue, item_master WHERE item_inward.item_id = item_issue.item_id AND item_issue.item_id = item_master.id AND item_issue.student_id IN ( SELECT id FROM student WHERE store_no = ".$_GET['store_no']." ) AND MONTH (item_issue.date) = ".$_GET['month']." AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )) AS fake GROUP BY id");
          $grid=$this->add('Grid');
            $grid->addColumn('template','item')->setTemplate('<div class="hindi"><?$item?></div>');
            $grid->addColumn('text','quantity');
            $grid->addColumn('text','rate');
            $grid->addColumn('text','total','Amount');
            $grid->setSource($q);
            $total=$this->api->db->dsql()->expr("SELECT sum(total) AS total FROM ( SELECT DISTINCT item_issue.item_id AS id, item_master.`name` AS item, item_issue.quantity, item_inward.rate, ( item_issue.quantity * item_inward.rate ) AS total FROM item_inward, item_issue, item_master WHERE item_inward.item_id = item_issue.item_id AND item_issue.item_id = item_master.id AND item_issue.student_id IN ( SELECT id FROM student WHERE store_no = ".$_GET['store_no']." ) AND MONTH (item_issue.date) = ".$_GET['month']." AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )) AS fake")->getOne();
           $this->add('H5')->set('Total : '.$total);
        }
    }
     function render(){
        $this->api->template->del("Menu");
        $this->api->template->del("logo");
        $this->api->template->trySet("Content","")  ;
        $this->api->template->trySet("Footer","")  ;
        
        parent::render();
    }
}