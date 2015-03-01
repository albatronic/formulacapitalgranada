<?php

/**
 * Description of IndexController
 *
 * @author Administrador
 */
class Error404Controller extends ControllerProject {

    var $entity = "Error404";

    public function IndexAction() {
        
        header("HTTP/1.0 404 Not Found");
        
        $this->values['seccion'] = new GconSecciones($this->request['IdEntity']);
        
        return parent::IndexAction();
    }

}