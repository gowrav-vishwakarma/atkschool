<?php

class Scholar_CRUD extends View_CRUD {

    function formSubmit($form) {
        if ($form->model->id) {
//            TODO- check if class is dirty or changed and if fee associated with the old class is still applied to this 
//            STUDENT then throw messgae and return 
        } else {
            $scl = $this->add('Model_Scholar');
            $scl->addCondition('scholar_no', $this->form->data['scholar_no']);
            $scl->tryLoadAny();
            if ($scl->loaded()) {
                $this->form->displayError('scholar_no', "Duplicate Scholar No " . $this->form->data['scholar_no']);
                return;
            }
        }

        parent::formSubmit($form);

        $this->form->model->associateClassFeeses();
    }

    function formSubmitSuccess() {
        parent::formSubmitSuccess();
    }

}
