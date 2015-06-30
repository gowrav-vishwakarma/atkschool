<?php
class page_store_reports extends Page{
  function page_index() {
    // parent::init();
    $acl=$this->add( 'xavoc_acl/Acl' );
    $tabs=$this->add( 'Tabs' );
    $tabs->addTabURL( "./storealltlist", "Store Alloted List" );
    $tabs->addTabURL( "./recipt", "Reciept" );
    $tabs->addTabURL( "./toatlReport", "Fee & Store Report" );
  }

  function page_storealltlist() {
    $acl=$this->add( 'xavoc_acl/Acl' );
    $f=$this->add( 'Form', null, null, array( 'form_horizontal' ) );


    $class=$f->addField( 'dropdown', 'class' )->setEmptyText( 'p;u d{kk' );
    $class->setModel( 'Class' );
    $class->setAttr( 'class', 'hindi' );

    $category=$f->addField( 'dropdown', 'category' )->setEmptyText( 'All' );
    $category->setValueList( array( null=>'ALL', '0'=>'Private', '1'=>'Scholared' ) );
    $f->addSubmit( 'Print' );
    if ( $f->isSubmitted() ) {
      $this->js()->univ()->newWindow( $this->api->url( "./list", array( "class"=>$f->get( 'class' ), "category"=>$f->get( 'category' ) ) ), null, 'height=689,width=1246,menubar=1' )->execute();
    }
  }

  function page_storealltlist_list() {

    $this->api->stickyGET( 'class' );
    $this->api->stickyGET( 'category' );

    $m=$this->add( 'Model_Students_Current' );
    $m->addCondition( 'ishostler', true );
    $m->addExpression( "father_name" )->set( function( $m, $q ) {
        return $m->refSQL( "scholar_id" )->fieldQuery( "father_name" );
      } );
    $m->setOrder( 'store_no' );
    if ( $_GET['class'] ) {
      $m->addCondition( 'class_id', $_GET['class'] );
    }

    if ( $_GET['category']!=null ) {
      $m->addCondition( 'isScholared', $_GET['category'] );
    }
    $grid=$this->add( 'Grid' );

    $grid->setModel( $m, array( 'store_no', 'scholar', 'father_name', 'class' ) );
    $grid->setFormatter( "scholar", "hindi" );
    $grid->setFormatter( "class", "hindi" );
    $grid->setFormatter( "father_name", "hindi" );

  }

  function page_recipt() {
    $acl=$this->add( 'xavoc_acl/Acl' );
    $form=$this->add( 'Form', null, null, array( 'form_horizontal' ) );


    $store_no=$form->addField( 'line', 'store_no' )->setNotNull();
    $month=$form->addField( 'dropdown', 'month' );//->setEmptyText('p;u d{kk');
    $month->setValueList( array( 0=>"----",
        1=>"Jan",
        2=>"Feb",
        3=>"March",
        4=>"April",
        5=>"May",
        6=>"Jun",
        7=>"July",
        8=>"Aug",
        9=>"Sep",
        10=>"Oct",
        11=>"Nov",
        12=>"Dec"
      ) );
    $form->addsubmit( 'Print' );

    if ( $form->isSubmitted() ) {
      // throw $this->exception(strpos($form->get('store_no'),'-'));

      // $form->displayError('store_no',strpos($form->get('store_no'),'-'));
      if(strpos($form->get('store_no'),'-')!== false){
        $this->js()->univ()->newWindow( $this->api->url( "store/MultiRecieptPrint", array( "month"=>$form->get( 'month' ), "store_no"=>$form->get( 'store_no' ) ) ), null, 'height=689,width=1246,menubar=1' )->execute();
      }

      if ( $form->get( 'month' )==0 ) {
        $this->js()->univ()->newWindow( $this->api->url( "store/recieptAll", array( "month"=>$form->get( 'month' ), "store_no"=>$form->get( 'store_no' ) ) ), null, 'height=689,width=1246,menubar=1' )->execute();
      }else {
        $this->js()->univ()->newWindow( $this->api->url( "store/reciept", array( "month"=>$form->get( 'month' ), "store_no"=>$form->get( 'store_no' ) ) ), null, 'height=689,width=1246,menubar=1' )->execute();
      }
    }


  }

  function page_toatlReport() {
    $acl=$this->add( 'xavoc_acl/Acl' );
    $form=$this->add( 'Form', null, null, array( 'form_horizontal' ) );


    $store_no=$form->addField( 'line', 'store_no' )->setNotNull();

    $form->addsubmit( 'Print' );

    if ( $form->isSubmitted() ) {
        $this->js()->univ()->newWindow( $this->api->url( "store/totalreciept", array("store_no"=>$form->get( 'store_no' ) ) ), null, 'height=689,width=1246,menubar=1' )->execute();
    }

  }

  function render() {
    $this->api->template->del( "Menu" );
    $this->api->template->del( "logo" );
    $this->api->template->trySet( "Content", "" )  ;
    $this->api->template->trySet( "Footer", "" )  ;

    parent::render();
  }
}
