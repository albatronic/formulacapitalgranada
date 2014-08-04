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
        
        $this->values['dominio'] = $this->varWeb['Pro']['globales']['dominio'];
        $this->values['empresa'] = $this->varWeb['Pro']['globales']['empresa'];
        
        return parent::IndexAction();
    }
}

?>
