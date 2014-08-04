<?php

/**
 * Description of ContactoController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @version 1.0 26-nov-2012
 */
class ContactoController extends ControllerProject {

    var $entity = "Contacto";
    var $formContacta = array();

    public function IndexAction() {

        switch ($this->request['METHOD']) {
            case 'GET':
                $this->formContacta = array(
                    'campos' => array(
                        'Nombre' => array('valor' => 'Nombre', 'error' => false),
                        'Email' => array('valor' => 'Email', 'error' => false),
                        'Asunto' => array('valor' => 'Asunto', 'error' => false),
                        'Mensaje' => array('valor' => 'Mensaje', 'error' => false),
                    ),
                );
                break;

            case 'POST':
                $this->formContacta = $this->request['campos'];

                if ($this->Valida()) {
                    if ((file_exists('docs/plantillaMailVisitante.htm')) and (file_exists('docs/plantillaMailWebMaster.htm'))) {

                        $mailer = new Mail($this->varWeb['Pro']['mail']);
                        $envioOk = $this->enviaVisitante($mailer,'docs/plantillaMailVisitante.htm');

                        if ($envioOk)
                            $envioOk = $this->enviaWebMaster($mailer,'docs/plantillaMailWebMaster.htm');

                        $this->formContacta['accion'] = 'envio';
                        $this->formContacta['resultado'] = $envioOk;
                        $this->formContacta['mensaje'] = ($envioOk) ?
                                $this->varWeb['Pro']['mail']['mensajeExito'] :
                                $this->varWeb['Pro']['mail']['mensajeError'];

                        unset($mailer);
                        
                    } else {
                        $this->formContacta['accion'] = 'envio';
                        $this->formContacta['resultado'] = false;
                        $this->formContacta['mensaje'] = "No se han definido las plantillas.";
                    }
                }
                break;
        }

        $this->values['formContacta'] = $this->formContacta;

        return parent::IndexAction();
    }

    /**
     * Envía el correo de confirmación al visitante
     * en base a la plantilla htm $ficheroPlantilla.
     * 
     * @param Mail $mailer objeto mailer
     * @param string $ficheroPlantilla El archivo que tiene la plantilla htm a enviar
     * @return boolean TRUE si se envío con éxito
     */
    private function enviaVisitante($mailer,$ficheroPlantilla) {
                       
        $plantilla = file_get_contents($ficheroPlantilla);
        $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
        $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
        $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
        $plantilla = str_replace("#EMPRESA#", $this->varWeb['Pro']['globales']['empresa'], $plantilla);
        $plantilla = str_replace("#MAIL#", $this->varWeb['Pro']['globales']['from'], $plantilla);

        return $mailer->send(
                $this->formContacta['campos']['Email']['valor'], $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Hemos recibido su mensaje', $plantilla, array()
        );
    }

    /**
     * Envía el correo de confirmación al webmaster
     * en base a la plantilla htm $ficheroPlantilla.
     * 
     * @param Mail $mailer objeto mailer
     * @param string $ficheroPlantilla El archivo que tiene la plantilla htm a enviar
     * @return boolean TRUE si se envío con éxito
     */    
    private function enviaWebMaster($mailer,$ficheroPlantilla) {

        $plantilla = file_get_contents($ficheroPlantilla);
        $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
        $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
        $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
        $plantilla = str_replace("#VISITANTE#", $this->formContacta['campos']['Nombre']['valor'], $plantilla);
        $plantilla = str_replace("#MAIL#", $this->formContacta['campos']['Email']['valor'], $plantilla);
        $plantilla = str_replace("#TELEFONO#", $this->formContacta['campos']['Telefono']['valor'], $plantilla);
        $plantilla = str_replace("#ASUNTO#", $this->formContacta['campos']['Asunto']['valor'], $plantilla);
        $plantilla = str_replace("#MENSAJE#", $this->formContacta['campos']['Mensaje']['valor'], $plantilla);
        
        return $mailer->send(
                $this->varWeb['Pro']['mail']['from'], $this->formContacta['campos']['Email']['valor'], $this->formContacta['campos']['nombre']['valor'], 'Ha recibido un mensaje en la web', $plantilla, array()
        );
     
    }

    private function Valida() {

        $error = false;

        if (!isset($this->formContacta['leidoPolitica']['valor']))
            $this->formContacta['leidoPolitica']['valor'] = '';

        foreach ($this->formContacta as $campo => $valor) {
            $valor = trim(str_replace($campo, "", $valor['valor']));
            $errorCampo = ($valor == '');
            $this->formContacta['campos'][$campo]['valor'] = $valor;
            $this->formContacta['campos'][$campo]['error'] = $errorCampo;
            $error = ($error or $errorCampo);
        }

        // Comprobar la validez ortográfica de la dirección de correo
        $mail = new Mail($this->varWeb['Pro']['mail']);
        if (!$mail->compruebaEmail($this->formContacta['campos']['Email']['valor'])) {
            $this->formContacta['campos']['Email']['error'] = 1;
            $error = true;
        }

        return !$error;
    }

}

?>
