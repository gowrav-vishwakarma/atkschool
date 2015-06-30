<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class page_master_school_fee extends Page {

    function init() {
        parent::init();
        
           $shBtn = $this->add('Button')->set('ADD NEW FEE');
           $hdBtn = $this->add('Button')->set('HIDE ADD FORM');
           $hdBtn->js(true)->hide();
        
        $form = $this->add('Form');
        $form->addField('dropdown', 'feehead')->setModel('Model_Feehead');
        $form->addField('line', 'name')->setNotNull();
        $form->addField('line', 'amount')->setNotNull();
        $form->addField('line', 'scholaredamount','Scholared Amount')->setNotNull();
        $form->addField('checkbox', 'isOptional');
        foreach ($this->add('Model_Class') as $c) {
            $form->addField('checkbox', 'forclass_' . $c['id'], $c['name']);
        }
        $form->addSubmit("ADD FEE");

        $form->js(true)->hide();
        $shBtn->js('click', array(
            $shBtn->js()->hide(),
            $form->js()->show('fast'),
            $hdBtn->js()->show()
                ));
        $hdBtn->js('click', array(
            $hdBtn->js()->hide(),
            $form->js()->hide('fast'),
            $shBtn->js()->show()
                ));

        $g = $this->add('Grid');
        $g->js(true)->addClass('reloadable');
        
        $g->js('mygridload')->reload();
        
        if ($form->isSubmitted()) {
            try {

                $m = $this->add('Model_Fee');
                $m->set('feehead_id', $form->get('feehead'));
                $m->set('name', $form->get('name'));
                $m->set('amount', $form->get('amount'));
                $m->set('scholaredamount',$form->get('scholaredamount'));
                $m->set('isOptional', $form->get('isOptional'));
                $m->save();
                $classTemp = $this->add('Model_Class');
                foreach ($this->add('Model_Class') as $c) {
                    if ($form->get('forclass_' . $c['id'])) {
                        $classTemp->load($c['id']);
                        $classTemp->addFee($m);
                        $classTemp->unload();
                    }
                }
//                if (!$m->get('isOptional'))
//                    $m->makeCompulsory();
            } catch (Exception $e) {
                $this->js()->univ()->errorMessage($e->getMessage())->execute();
                return;
            }
        $form->js(null,$g->js()->reload())->reload()->execute();
            
        }


        
        $m = $this->add('Model_Fee');
        $m = $g->setModel($m);
        if ($g) {
            $g->addColumn('expander', 'class_associationEdit');
        }
    }

}