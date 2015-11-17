<?php
class Model_Mesh_Item extends Model_Item{
	function init(){
		parent::init();

		$this->addCondition('category_id',2);
		$this->hasMany('Mesh_ItemInward','item_id');
		$this->hasMany('Mesh_ItemConsume','item_id');
	
		
		$this->addExpression("TotalMeshInwardStock")->set(function ($m,$q){
			$itm=$m->add('Model_Mesh_ItemInward');
			// $itm->join('bill_master.id','bill_id');//->addField('session_id');
			$itm->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$m->getField('id'));
			return $itm->sum('quantity');

		})->caption('Inward Stock (current year)');		

		$this->addExpression("TotalConsume")->set(function ($m,$q){
				return $m->refSQL("Mesh_ItemConsume")->sum('quantity');
		})->caption('Total Consume Qty');

		$this->addExpression("current_consume")->set(function ($m,$q){
			$issue=$m->add('Model_Mesh_ItemConsume');
			$issue->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$issue->addCondition('item_id',$m->getField('id'));
				return $issue->sum('quantity');
		})->caption('current Year Consume Qty');
		
		
	}
}