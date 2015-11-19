<?php
class page_masters_scholars extends Page {
	function init() {
		parent::init();
		$acl=$this->add( 'xavoc_acl/Acl' );

		$form=$this->add('Form',null,null,array('form_horizontal'));
		$session_field=$form->addField('dropdown','session');
		$session_field->setModel('Session');
		$form->addSubmit('Get List');

		try{
			$crud = $this->add( 'CRUD',array('allow_add'=>false));
			if ( $crud->grid ) {
				$crud->grid->addPaginator( 10 );
				$crud->grid->addColumn( 'sno', 'sno' );
			}


			$sc=$this->add( 'Model_Scholar' );

			// $sc->getField( 'image_url' )->system( false );
			$sc->getField( 'student_image' )->system( false );

			$sc->_dsql()->del( 'order' )->order( 'scholar_no', 'desc' );



			if ( $crud->grid ) {
				$crud->grid->addQuickSearch( array( 'fname', 'scholar_no','category' ) );
			}

			$st=$sc->leftJoin( 'student.scholar_id', 'id' );
			$st->hasOne( 'Class', 'class_id' );
			$st->addField( 'session_id' );
			
			// if($crud->grid){
			// }

			if($_GET['session']){
				$this->api->stickyGET('session');
				$sc->addCondition('session_id',$_GET['session']);
			}

			$crud->setModel( $sc);
				
			// if($crud->grid) $crud->grid->dq->where( 'session_id', $this->add( 'Model_Sessions_Current' )->tryLoadAny()->get( 'id' ) );

			if ( $crud->grid ) {

				// $crud->grid->setFormatter( 'class', 'hindi' );
			}

			if($form->isSubmitted()){
				
				$crud->grid->js()->reload(array('session'=>$form->get('session')))->execute();
			}	
		}catch( Exception $e ) {
			throw $e;
			$this->js()->univ()->errorMessage( $e->getMessage() )->execute();

		}
	}

	function init_old_saved() {
		parent::init();
		$acl=$this->add( 'xavoc_acl/Acl' );
		$crud = $this->add( 'CRUD', array( "allow_add"=>false, "allow_del"=>false ) );
		if ( $crud->grid ) {
			$crud->grid->addPaginator( 10 );
			$crud->grid->addColumn( 'sno', 'sno' );
		}

		$st=$this->add( 'Model_Scholars_Current' );//->debug();
		$st->_dsql()->del( 'order' )->order( 'scholar_no', 'desc' );
		// $st->_dsql()->order('fname','asc');
		$crud->setModel( $st );
		if ( $crud->form ) {
			$crud->form->getElement( 'class_id' )->setAttr( 'class', 'hindi' );
			if ( $crud->form->model->loaded() ) {
				$crud->form->model->addCondition( 'session_id', $this->add( 'Model_Sessions_Current' )->tryLoadAny()->get( 'id' ) );
			}
		}
		if ( $crud->grid ) {
			$crud->grid->setFormatter( 'class', 'hindi' );
			// $crud->grid->addQuickSearch('fname', 'scholar_no');
		}
	}
}
