<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class page_master_school_fee_class_associationEdit extends Page {

    function initMainPage() {
        parent::init();

        $this->api->stickyGET('fee_id');
        $fee = $this->add('Model_Fee');
        $fee->load($_GET['fee_id']);

        $fee_associated_with_class = array();
        $classes = $this->add('Model_Class');
        foreach ($fee->ref('Class_Fee_Mapping') as $cf) {
            $fee_associated_with_class[] = $classes->tryLoad($cf['class_id'])->get('name');
            $classes->unload();
        }
        $classes->unload();
        $form = $this->add('Form');
        $form->addField('line', 'name')->setNotNull()->set($fee->get('name'));
        $form->addField('line', 'amount')->setNotNull()->set($fee->get('amount'));
        $form->addField('line', 'scholaredamount')->setNotNull()->set($fee->get('scholaredamount'));
        $form->addField('checkbox', 'isOptional')->set($fee->get('isOptional'));
        $form->addField('dropdown', 'feehead')->setModel('Feehead')->set($fee->get('feehead_id'));

        foreach ($classes as $cf) {
            $t = $form->addField('checkbox', 'forclass_' . $cf['id'], $cf['name']);
            if (in_array($cf['name'], $fee_associated_with_class))
                $t->set(true);
        }

        $form->addSubmit("Update");

        if ($form->isSubmitted()) {
            try{
            $fee->set('feehead_id', $form->get('feehead'));
            $fee->set('name', $form->get('name'));
            $fee->set('amount', $form->get('amount'));
            $fee->set('isOptional', $form->get('isOptional'));
            $fee->save();
            foreach($this->add('Model_Class')->getRows() as $class){
                $tc=$this->add('Model_Class');
                $tc->load($class['id']);
                if($form->get('forclass_'.$class['id'])){
                    if(!$tc->isFeeAssociated($fee))
                        $tc->addFee($fee);
                }else{
                    if($tc->isFeeAssociated($fee))
                        $tc->removeFee($fee);
                }
                $tc->unload();
            }
//            if(!$fee->get('isOptional'))
//                $fee->makeCompulsory();
            }catch(Exception $e){
                $form->js()->univ()->errorMessage($e->getMessage())->execute(); 
            }
//            $this->js()->_selectorThis()->reload()->execute();
            $form->js(null,array(
                $form->js(true)->closest('.reloadable')->trigger('mygridload'),
                $form->js()->univ()->closeExpander(),
                $form->js()->reload(),
                    ))->univ()->successMessage("Modifications done")->execute();
        }
    }

}