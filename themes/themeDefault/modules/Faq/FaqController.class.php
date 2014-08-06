<?php

/**
 * Description of FaqController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 7-Agosto-2014
 *
 */
class FaqController extends ControllerProject {

    protected $entity = "Faq";

    public function IndexAction() {

        $this->values['faq'] = Contenidos::getContenidosSeccion($this->request['IdEntity'], 1, 9999);
        $this->values['servicios'] = Servicios::getServicios(3, true); // Servicios Particulares
        
        return parent::IndexAction();
    }

}
