<?php

class Model_Item extends Model_Table{
	var $table="item_master";

	function init(){
		parent::init();

		$this->hasOne('Item_Category','category_id')->display(array('grid'=>'hindi'))->mandatory("Select Category First");
		$this->addField('name')->caption('Item')->display('hindi');
		// $this->addField('category');
		$this->addField('is_stationory')->type('boolean')->system(true);
		$this->addField('stock')->system(true);
		$this->hasMany('Item_Inward','item_id');
		$this->hasMany('Item_Issue','item_id');


		$this->addExpression("LastPurchasePrice")->set(function ($m,$q){
			return $m->refSQL('Item_Inward')->dsql()->del('field')->field('rate')->limit(1)->order('id','desc');
		});
		
		$this->addExpression("CurrentInwardStock")->set(function ($m,$q){
			$itm=$m->add('Model_Item_Inward');
			$itm->join('bill_master.id','bill_id');//->addField('session_id');
			$itm->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$m->getField('id'));
			return $itm->sum('quantity');

		})->caption('Inward Stock (current year)');

		$this->addExpression("TotalInwardStock")->set(function ($m,$q){
			$itm=$m->add('Model_Item_Inward');
			$itm->join('bill_master.id','bill_id');//->addField('session_id');
			$itm->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$m->getField('id'));
			return $itm->sum('quantity');

		})->caption('Inward Stock (Total)');

		

		// $this->debug();

		$this->addExpression("TotalIssued")->set(function ($m,$q){
				return $m->refSQL("Item_Issue")->sum('quantity');
		})->caption('Total Issue Qty');

		$this->addExpression("current_Issued")->set(function ($m,$q){
			$issue=$m->add('Model_Item_Issue');
			$issue->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$issue->addCondition('item_id',$m->getField('id'));
				return $issue->sum('quantity');
		})->caption('current Year Issue Qty');

		$this->addExpression('consume')->set(function($m,$q){
			$itm=$m->add('Model_Consume');
			// $itm->addCondition('session_id','<',(int)$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$q->getField('id'));

			$t_qty=$itm->sum('quantity');/*-$itm_c->sum('quantity')*//*.*//*')';*/
			return $t_qty;

		});


		$this->addExpression("instock")->set('id')->display(array("grid"=>'instock'));

	    		$this->addHook('beforeDelete',$this);
	    		$this->add('dynamic_model/Controller_AutoCreator');
	   	}
	function beforeDelete(){
		if($this->ref('Item_Inward')->count()->getOne())
			throw $this->exception("You Can not Delete Item, It Conatains Bill(s) and Inward 0r Issued Entries");
	}
}