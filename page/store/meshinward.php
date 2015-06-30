<?php
class page_store_meshinward extends Page{
	function init(){
		parent::init();
		$this->api->stickyGET('party_id');
		$form=$this->add('Form');
		$mesh_inward=$this->add('Model_Mesh_ItemInward');
		$mesh_inward->addCondition('party_id',$_GET['party_id']);
		$mesh_inward->addCondition('session_id',$this->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
		$form->setModel($mesh_inward);
		$form->getElement('item_id')->addClass('hindi');
		$form->addSubmit('Inward');
		if($form->isSubmitted()){
			$form->update();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Inward Successfully')->closeDialog()->execute();
		}
	}
}