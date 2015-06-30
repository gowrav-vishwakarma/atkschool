<?php
class page_master_hostel_image extends Page
{
    function init()
    {
        parent::init();
        $this->api->stickyGET('guardian_id'); 
                
        
        $f_id=$this->api->db->dsql()->table('scholar_guardian')->field('image')->where('id',$_GET['guardian_id'])->do_getOne();
        $f_oname=$this->api->db->dsql()->table('filestore_file')->field('original_filename')->where('id',$f_id)->do_getOne();
        $f_name=$this->api->db->dsql()->table('filestore_file')->field('filename')->where('id',$f_id)->do_getOne();

        If(!$f_name==null)
            $v=$this->add('View')->setElement('img')->setAttr('src','upload/'.$f_name);
        else
            $this->add('H1')->set('No Image Uploaded for this Guardian');
        
        
        
    }
}