<?php

class page_data_store_issue extends Page{
    
    function initMainPage()
    {
        parent::init();
        $form=$this->add('Form',null,null,array('form_horizontal'));
        $store_no=$form->addField('line','store_no')->setNotNull();
        $store_no->js(true)->univ()->numericField();

        $form->addSubmit("search")->js(true)->hide();
        $name=$form->addField('line','name');
                $name->setAttr('class','hindi');
                $name->disable();
        $class=$form->addField('line','class')->disable()->setAttr('class','hindi');
     
if($form->isSubmitted())
{
try
{
    
$s_name=$this->api->db->dsql()->expr("SELECT scholars_master.hname AS `name` FROM scholars_master, student WHERE scholars_master.id = student.scholar_id AND student.store_no = ".$form->get('store_no'))->do_getOne();    
$s_class=$this->api->db->dsql()->expr("SELECT CONCAT( class_master.`name`, \" \", class_master.section ) AS class FROM class_master, student WHERE class_master.id = student.class_id AND student.store_no = ".$form->get('store_no'))->do_getOne();
if($s_name=="")
    throw new Exception; 

    
   $this->js()->find('.atk4_loader')->not('.atk-form-field')->atk4_loader('loadURL', array($this->api->url('./search'),'store_no'=>$form->get('store_no'),$name->js()->val($s_name), $class->js()->val($s_class)))->execute(); 

}
 catch (Exception $e)
{
     $this->js()->univ()->alert('Store Number Does not Exist')->execute();
}


}
        $view=$this->add('View');
        $view->js(true)->atk4_load($this->api->url('./search'))->set('Loading..');
    }
    
    
    function page_search()
    {
        $this->api->stickyGET('store_no');
      
        if($_GET['store_no'])
        {
           //$this->add('h1')->set('hi'.rand(1,100));
            $student_id=$this->api->db->dsql()->expr("select student.id from student where store_no=".$_GET['store_no'])->do_getOne();
            $m=$this->add('Model_Store_Issue');
            
            $m->getElement('student_id')->system(true);
            //$m->getElement('date')->system(true);
            
            
            
            $total=$this->api->db->dsql()->expr("SELECT sum(total) AS total FROM ( SELECT DISTINCT item_issue.item_id, ( item_issue.quantity * item_inward.rate ) AS total FROM item_inward, item_issue, item_master WHERE item_inward.item_id = item_issue.item_id AND item_issue.item_id = item_master.id AND item_issue.student_id = ".$student_id." AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )) AS fake")->do_getOne();
            $q=$this->api->db->dsql()->expr("SELECT DISTINCT item_issue.item_id,item_issue.id as id,MONTHNAME(item_issue.date) as month ,item_master.`name` as item, item_issue.quantity, item_inward.rate, ( item_issue.quantity * item_inward.rate ) AS total FROM item_inward, item_issue, item_master WHERE item_inward.item_id = item_issue.item_id AND item_issue.item_id = item_master.id AND item_issue.student_id = ".$student_id." AND item_issue.session_id IN ( SELECT id FROM session_master WHERE session_master.iscurrent = TRUE )");
            
            $f=$this->add('Form',null,null,array('form_horizontal'));
            $f->setModel($m);
            $drp_item=$f->getElement('item_id');
            $drp_item->setEmptyText('p;u')->setAttr('class','hindi');
            
            $txt_quantity=$f->getElement('quantity')->setNotNull();
            $txt_quantity->js(true)->univ()->numericField();
            $f->addSubmit('Issue')->js(true)->hide();
            
            
            $p= $this->add('View')->addClass('atk-box ui-widget-content ui-corner-all')->addStyle('background','#ddd');
           $h=$p->add('H1')->set("Total:".$total);
            $grid=$p->add('Grid');
            $grid->addColumn('template','item')->setTemplate('<div class="hindi"><?$item?></div>');
            $grid->addColumn('text','quantity');
            $grid->addColumn('text','rate');
            $grid->addColumn('text','total');
            $grid->addColumn('text','month');
            $grid->addColumn('confirm','delete');
            $grid->setSource($q);
            
            if($_GET['delete'])
            {
                $this->api->db->dsql()->expr("delete from item_issue where id=".$_GET['delete'])->execute();
                $grid->js()->reload($h->js()->reload())->execute();
                //$grid->js()->univ()->successMessage($_GET['delete'])->execute();
            }
                       
            if($f->isSubmitted())
            {
            $count=$this->api->db->dsql()->expr("select count(*) from reciept where store_no=".$_GET['store_no']." and MONTH(reciept_month)=MONTH(CURRENT_DATE())")->getOne();
             
            if($count==0)
                $this->api->db->dsql()->expr("insert into reciept(store_no,reciept_month) values(".$_GET['store_no'].",'".$f->get('date')."')")->execute();
          
                
                
            $q_inward= $this->api->db->dsql()->expr("select sum(item_inward.quantity) from item_inward where item_id= ".$f->get('item_id')." and session_id=(select session_master.id from session_master where iscurrent=true)")->do_getOne();
            $q_issue=$this->api->db->dsql()->expr("select sum(item_issue.quantity) from item_issue where item_id= ".$f->get('item_id')." and session_id=(select session_master.id from session_master where iscurrent=true)")->do_getOne();
            if($q_inward<$q_issue + $f->get('quantity'))
            {
                $this->js()->univ()->alert($q_inward-$q_issue." items of this type remaining")->execute();
                 return;
            } 
            $m->set('student_id',$student_id);
            $m->set('date',$f->get('date'));
            $m->set('item_id',$f->get('item_id'));
            $m->set('quantity',$f->get('quantity'));
            $m->save();
            
            $f->js(null,array($grid->js()->reload(),$h->js()->reload()))->execute();
            
            }
            
        }
        else
        {
         $v= $this->add('View_Info')->set('Enter Store Number First');//->add('View');
         
        }
    }
    
}