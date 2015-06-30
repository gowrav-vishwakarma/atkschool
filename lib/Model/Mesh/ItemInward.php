<?php
class Model_Mesh_ItemInward extends Model_Table{
	public $table='mesh_item_inward';
	function init(){
		parent::init();
		$this->hasOne('Mesh_Item','item_id')->mandatory(true);
		$this->hasOne('Party','party_id')->mandatory(true);
		$this->hasOne('Session','session_id');
		$this->addField('quantity');
		$this->addfield('unit')->enum(array('Packet','Kg','Liter'));
		$this->addfield('rate')->type('money');
		$this->addfield('date')->type('date')->defaultValue(date('Y-m-d'));

		$this->addExpression('Amount')->set('quantity * rate');
		$this->add('dynamic_model/Controller_AutoCreator');

	}
}