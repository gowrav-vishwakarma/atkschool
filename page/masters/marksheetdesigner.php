<?php
class page_masters_marksheetdesigner extends Page{
	function page_index(){
		// parent ::init();
		$acl=$this->add('xavoc_acl/Acl');
		$crud=$this->add("CRUD");

		$crud->setModel("MS_Designer");
		if($crud->form){
			$crud->form->getElement('class_id')->setAttr('class','hindi');
		}

		if($crud->grid){
			$crud->grid->addColumn("Expander","sections","Marks Sections");
			$crud->grid->setFormatter('class','hindi');
		}

	}

	function page_sections(){
		$this->api->stickyGET('marksheet_designer_id');
		$md=$this->add('Model_MS_Designer')->load($_GET['marksheet_designer_id']);
		$cls=$md['class_id'];
		$this->api->memorize('marksheet_class',$md['class_id']);

		$crud=$this->add("CRUD");
		$this->add('View_Info')->set('B1=>A+B=>B=>Two Total;B2=>A+B+C+D+E=>E=>Five Total');
		$mb=$md->ref('MS_Sections');
		// $mb->addCondition('marksheet_designer_id',$_GET['marksheet_designer_id']);
		/*$mb->addCondition('class_id',$this->api->recall('marksheet_class'));*/
		$crud->setModel($mb);

		if($crud->grid){
			$crud->grid->addColumn("Expander","sectionblocks","Section Blocks");
			$crud->grid->addColumn("Expander","sectionsubjects","Section Subject");
		}
	}

	function page_sections_sectionblocks(){
		$this->api->stickyGET('marksheet_sections_id');
		$sections = $this->add('Model_MS_Sections')->load($_GET['marksheet_sections_id']);

		$blocks=$sections->ref('MS_SectionBlocks');
		$crud=$this->add('CRUD');
		$crud->setModel($blocks);
		if($crud->grid){
			$crud->grid->addColumn("Expander","blocksexam","Block Exams");
		}
	}

	function page_sections_sectionsubjects(){
		$this->api->stickyGET('marksheet_sections_id');

		$section = $this->add('Model_MS_Sections')->load($_GET['marksheet_sections_id']);
		
		$options=array(
				'leftModel' => $section,
				'mappingModel' => 'MS_SectionSubjects',
				'leftField' => 'marksheet_section_id',
				'rightField' => 'subject_id',
				'rightModel' => 'Subject',
				'deleteFirst' => true,
				'maintainSession' => true
			);		
		// $this->add('View')->set('Hi');
		$p=$this->add('View_Mapping',$options);
	}

	function page_sections_sectionblocks_blocksexam(){
		$this->api->stickyGET('marksheet_section_blocks_id');
		$block = $this->add('Model_MS_SectionBlocks')->load($_GET['marksheet_section_blocks_id']);

		$exams = $block->ref('MS_BlockExams');

		$crud=$this->add('CRUD');
		$crud->setModel($exams);
		if($crud->form){
			$crud->form->getElement('exammap_id')->model->_dsql()->where('class_id',$this->api->recall('marksheet_class'));
			$crud->form->getElement('exammap_id')->setAttr('class','hindi');
		}
		if($crud->grid){
			$crud->grid->setFormatter('exammap','hindi');
		}

	}

}