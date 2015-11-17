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
		$item_mesh->addExpression('previous_mesh_stocks_inword')->set(function($m,$q){
			$mesh_itm_inward=$m->add('Model_Mesh_ItemInward');
			$mesh_itm_inward->addCondition('session_id','<',(int)$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$mesh_itm_inward->addCondition('item_id',$q->getField('id'));
			$t_qty=$mesh_itm_inward->sum('quantity');
			return $t_qty;
		});

		$item_mesh->addExpression('previous_mesh_stocks_outword')->set(function($m,$q){
			$consume = $m->add('Model_Mesh_ItemConsume');
			$consume->addCondition('session_id','<',(int)$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$consume->addCondition('item_id',$q->getField('id'));
			$i_qty = $consume->sum('quantity');
			return $i_qty;
		});

		$this->grid->addMethod('format_prevstock',function($g,$field){
			$g->current_row[$field] = $g->model['previous_mesh_stocks_inword'] - $g->model['previous_mesh_stocks_outword'];
		});
		$this->grid->addColumn('prevstock','previous_stock');

		$item_mesh->addExpression("last_purchase_price")->set(function ($m,$q){
			return $m->refSQL('Mesh_ItemInward')->dsql()->del('field')
												->field('rate')->limit(1)->order('id','desc');
		});

		$this->grid->addMethod('format_tstock',function($g,$field){
			$g->current_row[$field]=$g->current_row['previous_stock'] + $g->current_row['TotalMeshInwardStock'];
		});
		$this->grid->addColumn('tstock','total_stock');

		$this->grid->addMethod('format_ctstock',function($g,$field){
			$g->current_row[$field]=$g->current_row['total_stock']- $g->current_row['current_consume'];
		});
		$this->grid->addColumn('ctstock','current_stock') ;

		$this->grid->setModel($item_mesh,array('name','last_purchase_price','TotalMeshInwardStock','TotalConsume','current_consume','previous_mesh_stocks_inword','previous_mesh_stocks_outword'));

		$this->grid->removeColumn('current_inward');
		$this->grid->removeColumn('TotalConsume');
		$this->grid->removeColumn('previous_mesh_stocks_inword');
		$this->grid->removeColumn('previous_mesh_stocks_outword');
		 $order=$this->grid->addOrder();
		 $order->move('total_stock','after','TotalMeshInwardStock')->now();
		 $order->move('current_stock','after','current_consume')->now();
		 $order->move('previous_stock','after','last_purchase_price')->now();
		
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

		$item->addExpression('previous_stocks_inword')->set(function($m,$q){
			$itm=$m->add('Model_Item_Inward');
			$itm->addCondition('session_id','<',(int)$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$itm->addCondition('item_id',$q->getField('id'));

			$t_qty=$itm->sum('quantity');/*-$itm_c->sum('quantity')*//*.*//*')';*/
			return $t_qty;

		});

		$item->addExpression('previous_stocks_outword')->set(function($m,$q){
			$issue = $m->add('Model_Item_Issue');
			$issue->addCondition('session_id','<',(int)$m->add('Model_Sessions_Current')->tryLoadAny()->get('id'));
			$issue->addCondition('item_id',$q->getField('id'));
			$i_qty = $issue->sum('quantity');
			return $i_qty;

		});

		$this->grid->setModel($item,array('name','LastPurchasePrice','inward','outward','TotalIssued','current_Issued','TotalInwardStock','previous_stocks_inword','previous_stocks_outword'));
		
		$this->grid->addMethod('format_stock',function($g,$field){
			$g->current_row[$field] = $g->model['previous_stocks_inword'] - $g->model['previous_stocks_outword'];
			// echo "string".$g->current_row['TotalInwardStock'];
			// $t_qty=$itm->ref('item_id')->get('TotalInwardStock')-$g->current_row['TotalIssued'];/*-$itm_c->sum('quantity')*//*.*//*')';*/
			// // return $t_qty
			// $g->current_row[$field]=$t_qty;
		});
		$this->grid->addColumn('stock','previouse_stock');

		$this->grid->addMethod('format_astock',function($g, $field){
			$g->current_row[$field]=$g->current_row['previouse_stock'] + $g->current_row['TotalInwardStock'];

		});
		$this->grid->addColumn('astock','Total_Stock');



		$this->grid->addMethod('format_totalqty',function($g,$field){
			$g->current_row[$field]=$g->current_row['Total_Stock']- $g->current_row['current_Issued'];
		});
		$this->grid->addColumn('totalqty','total_current_stock');
	

		$this->grid->removeColumn('inward');
		$this->grid->removeColumn('outward');
		$this->grid->removeColumn('previous_stocks_outword');
		$this->grid->removeColumn('previous_stocks_inword');
		$this->grid->removeColumn('TotalIssued');

		$order=$this->grid->addOrder();
   		$order->move('previouse_stock','after','LastPurchasePrice')->now();
   		$order->move('TotalInwardStock','after','previouse_stock')->now();
   		$order->move('Total_Stock','after','TotalInwardStock')->now();
   		$order->move('current_Issued','after','Total_Stock')->now();
   		$order->move('total_current_stock','after','current_Issued')->now();
  		// $$order->move($this->getElement('customer_email'),'first');

	}
}