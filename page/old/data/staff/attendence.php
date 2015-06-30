<?php

class page_data_staff_attendence extends Page {

    function init() {
        parent::init();
        
        $g=$this->add('Grid');
        
        $query = $this->api->db->dsql()
                ->expr("SELECT staff_master.hname AS `name`, staff_master.designation AS designation, staff_master.contact AS contact, staff_outward.action AS `status` FROM staff_master, staff_outward WHERE staff_outward.id IN ( SELECT MAX(id) FROM staff_outward GROUP BY staff_id ) AND staff_master.id = staff_outward.staff_id order by ename");
        $g->addColumn('template','name')->setTemplate('<div class="hindi"><?$name?></div>');
        $g->addColumn('template','status')->setTemplate('<div > <span class="<?$status?>"><?$status?></span></div>');
        $g->addColumn('template','designation')->setTemplate('<div class="hindi"><?$designation?></div>');
        $g->addColumn('text','contact');
        $g->setSource($query);
    }
}
