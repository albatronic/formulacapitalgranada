<?php

/**
 * Description of AvisoLegalController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 06-nov-2012
 *
 */
class AvisoLegalController extends ControllerProject {

    protected $entity = "AvisoLegal";

    public function IndexAction() {
        
        $this->values['dominio'] = $this->varWeb['Pro']['globales']['dominio'];
        $this->values['empresa'] = $this->varWeb['Pro']['globales']['empresa'];
        
        return parent::IndexAction();
    }
}

?>
