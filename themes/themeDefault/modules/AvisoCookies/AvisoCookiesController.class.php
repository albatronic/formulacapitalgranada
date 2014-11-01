<?php

/**
 * Description of AvisoCookiesController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 06-nov-2012
 *
 */
class AvisoCookiesController extends ControllerProject {

    protected $entity = "AvisoCookies";

    public function IndexAction() {

        $this->values['politica'] = new GconSecciones($this->request['IdEntity']);
        
        return parent::IndexAction();
    }
}
