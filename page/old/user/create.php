<?php

class page_user_create extends Page
{
    
    function initMainPage()
    {
        parent::init();
        $crud=$this->add('CRUD');
        $crud->setModel('Users',null,array('username','password'));
        if($crud->form){
            // make form flow in 2 columns
            $crud->form->setFormClass('stacked atk-row');
            $o=$crud->form->add('Order')
                ->move($crud->form->addSeparator('noborder span6'),'first')
                ->move($crud->form->addSeparator('noborder span5'),'middle')
                ->now();
        }    
          if($crud->grid)
          {
                $crud->grid->addButton('Add News')->js('click')->univ()->frameURL('news', $this->api->url('./news'));
          }
        
    }
    function page_news()
    {
        $f=$this->add('Form');
        $f->addField('text','text');
        $f->getElement('text')->setAttr('class','hindi');
        $f->addSubmit();
        if($f->isSubmitted())
        {
            $this->api->db->dsql()->expr("update news set name='".$f->get('text')."' where id=1")->execute();
            $this->js()->univ()->closeDialog()->successMessage('News Added successfully')->execute();
        }
    }
    
}