<?php

/**
 * Description of ContenidoController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 06-agosto-2014
 */
class ContenidoController extends ControllerProject {

    var $entity = "Contenido";

    public function IndexAction() {
        
        $this->values['menuLateral'] = Menu::getMenuN(2);
        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);
        
        return parent::IndexAction();
    }
}