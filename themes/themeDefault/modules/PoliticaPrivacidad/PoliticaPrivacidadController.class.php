<?php

/**
 * Description of PoliticaPrivacidadController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 26-nov-2012
 *
 */
class PoliticaPrivacidadController extends ControllerProject {

    protected $entity = "PoliticaPrivacidad";

    public function IndexAction() {

        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);

        $template = ($this->request['Template'] !== '') ? $this->entity . "/" . $this->request['Template'] :
                $this->controller . "/index.html.twig";
        
        return array(
            'template' => $template,
            'values' => $this->values,
        );
    }    
}
