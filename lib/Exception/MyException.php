<?php

class Exception_MyException extends Exception_StopInit {

    function init(){
        parent::init();
        $this->api->add('View_Error')->set('You are not allow to access this page');
    }
    function __construct($msg,$func=null,$shift=1,$code=0){
        // parent::__construct($msg,$code);
        // $this->collectBasicData($func,$shift,$code);
    }
    function getHTML($message=null){
        $html='<div>'.$message.'</div>';
        return $html;
    }
    function getMyTrace(){
        // return $this->my_backtrace;
    }
}