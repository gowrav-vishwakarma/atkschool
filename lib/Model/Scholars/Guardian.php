<?php

class Model_Scholars_Guardian extends Model_Scholars_GuardianAll{

	function init(){
		parent::init();

        $this->addCondition('is_active',true);

    }
}