<?php

/**
 * Description of ServiciosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 26-nov-2012
 */
class ServiciosController extends ControllerProject {

    var $entity = "Servicios";

    public function IndexAction() {

        switch ($this->request['Entity']) {
            case 'ServServicios':
                // Servicio desarrollado
                $this->values['servicio'] = Servicios::getServicioDesarrollado($this->request['IdEntity']);
                $template = "/servicio.html.twig";
                break;

            default:
                // Minificha con todos los servicios
                $this->values['servicios'] = Servicios::getServicios();
                $template = '/index.html.twig';
        }


        return array(
            'template' => $this->entity . $template,
            'values' => $this->values,
        );
    }

}
