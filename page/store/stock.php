<?php
class page_store_stock extends Page {
	public $grid;
	function init(){
		parent::init();
		$acl=$this->add('xavoc_acl/Acl');
		$form=$this->add('Form');
		$form_cat=$form->addField('dropdown','category')->setEmptyText('----')->setAttr('class','hindi');
		$form_cat->setModel('Item_Category');
		$form->addSubmit('GetList');
		$this->grid= $this->grid =$this->add('Grid');
		if($_GET['category']==2) //mess id
			$this->handelMess();
		else
			$this->handleOthers();
		
		// $this->grid->removeColumn('TotalInward');
		if($form->isSubmitted()){
			
			$this->grid->js()->reload(array("category"=>$form->get('category'),
										"filter"=>-1))->execute();
		}

		// $tab=$this->add('Tabs');
		// $tab->addTabURL('stationory','Stationory');
		// $tab->addTabURL('other','Other Item(Mess)');
	}

	function handelMess(){
		$item_mesh=$this->add('Model_Mesh_Item');
		$item_mesh->addExpression('total_inward')->set(function($m,$q){
			return $m->refSQL('Mesh_ItemInward')->sum('quantity');
		})->caption('Inward Stock ( Current Stock)');

		$item_mesh->addExpression('current_inward')->set(function($m,$q){
			$itm=$m->add('Model_Mesh_ItemInward');
			$itm->addCondition('session_id',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$q->getField('id'));
			return $itm->sum('quantity');
		});

		$item_mesh->addExpression('previous_stock')->set(function($m,$q){
			$itm=$m->add('Model_Mesh_ItemInward');
			$itm->addCondition('session_id','<=',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$q->getField('id'));

			$itm_c=$m->add('Model_Mesh_ItemConsume');
			$itm_c->addCondition('session_id','<=',$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm_c->addCondition('item_id',$q->getField('id'));

			return '('.$itm->sum('quantity')/*-$itm_c->sum('quantity')*/.')';
		})->caption('Last Year Remain Stock');


		$item_mesh->addExpression('total_outward')->set(function($m,$q){
			return $m->refSQL('Mesh_ItemConsume')->sum('quantity');
		})->caption('Total Consume');

		$item_mesh->addExpression("last_purchase_price")->set(function ($m,$q){
			return $m->refSQL('Mesh_ItemInward')->dsql()->del('field')
												->field('rate')->limit(1)->order('id','desc');
		});

		$this->grid->addMethod('format_tstock',function($g,$field){
			$g->current_row[$field]=$g->current_row['total_inward']+$g->current_row['previous_stock'];
		});

		$this->grid->addMethod('format_ctstock',function($g,$field){
			$g->current_row[$field]=$g->current_row['total_stock']-$g->current_row['total_outward'];
		});
		$this->grid->setModel($item_mesh,array('name','previous_stock','last_purchase_price','total_inward','current_inward','total_outward'));
		$this->grid->addColumn('ctstock','current_stock') ;

		// $this->grid->addMethod('format_prevstock',function($g,$field){

		// 	$g->current_row[$field]=$g->current_row['last_year_remain_stock'];
		// });


		// $this->addColumn('')
		$this->grid->addColumn('tstock','total_stock');
		// $this->grid->addColumn('prevstock','last_year_remain_stock');
		// $this->grid->removeColumn('total_inward');
		$this->grid->removeColumn('current_inward');
		// $this->grid->removeColumn('current_stock');
		 $order=$this->grid->addOrder()->move('total_stock','after','total_inward')->now();
		
	}

	function handleOthers(){
		$item=$this->add('Model_Item');
		if($_GET['filter']){
			$item->addCondition('category_id',$_GET['category']);
		}

		$item->addExpression('inward')->set(function($m,$q){
		return 	$m->refSQL('Item_Inward')->sum('quantity');
		});

		$item->addExpression('outward')->set(function($m,$q){
			$x=$m->add('Model_Item_Issue');
			$x->_dsql()->del('where');
			$x->addCondition('item_id',$q->getField('id'));
		return $x->sum('quantity');
		});



		$this->grid->setModel($item,array('name','LastPurchasePrice','inward','outward','TotalIssued','TotalInward'));
		
		$this->grid->addMethod('format_stock',function($g,$field){
			$g->current_row[$field]=$g->current_row['current_inward']-$g->current_row['outward'];
		});
		$this->grid->addColumn('stock','previouse_stock');

		$this->grid->addMethod('format_totalqty',function($g,$field){
			$g->current_row[$field]=$g->current_row['previouse_stock']+$g->current_row['TotalInward'];
		});
		$this->grid->addColumn('totalqty','total_current_stock');
		$this->grid->removeColumn('inward');
		$this->grid->removeColumn('outward');
	}
}