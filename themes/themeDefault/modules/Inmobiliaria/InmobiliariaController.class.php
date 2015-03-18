<?php

/**
 * Description of ContenidoController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 06-agosto-2014
 */
class InmobiliariaController extends ControllerProject {

    var $entity = "Inmobiliaria";

    public function IndexAction() {

        $this->values['menuLateral'] = Menu::getMenuN(2);
        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);

        $inmuebles = new Inmuebles();
        $this->values['inmuebles'] = $inmuebles->cargaCondicion("*");
        
        $template = ($this->request['Template'] !== '') ? $this->entity . "/" . $this->request['Template'] :
                $this->controller . "/index.html.twig";
        
        return array(
            'template' => $template,
            'values' => $this->values,
        );
    }

}
