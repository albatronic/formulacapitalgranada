<?php

/**
 * Description of LlamamosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 13-Agosto-2014
 *
 */
class LlamamosController extends ControllerProject {

    protected $entity = "Llamamos";

    public function IndexAction() {

        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);

        switch ($this->request['METHOD']) {
            case 'GET':
                $this->values['accion'] = "";
                if ($this->request[1] == "log") {
                    return $this->Log();
                } else {
                    mail($this->varWeb['Pro']['mail']['from'], "Credit Granada", "Han entrado en el formulario 'te llamamos'");
                    $fp = fopen("log/contacto.log", "a");
                    fwrite($fp, date("Y-m-d H:i:s") . ";" . $_SERVER['REMOTE_ADDR'] . ";Entran en 'te llamamos'" . "\n");
                    fclose($fp);
                }
                break;

            case 'POST':

                $this->values['accion'] = "envio";

                if (file_exists('docs/plantillaMailContacto.htm')) {
                    
                    $fp = fopen("log/contacto.log", "a");
                    fwrite($fp, date("Y-m-d H:i:s") . ";" . $_SERVER['REMOTE_ADDR'] . ";Envío en 'te llamamos';" . print_r($this->request,true) . "\n");
                    fclose($fp);
                    
                    $mailer = new Mail($this->varWeb['Pro']['mail']);
                    $envioOk = $this->enviaWebMaster($mailer, 'docs/plantillaMailContacto.htm');

                    $this->values['mensaje'] = ($envioOk) ?
                            $this->varWeb['Pro']['mail']['mensajeExito'] :
                            $this->varWeb['Pro']['mail']['mensajeError'];

                    unset($mailer);
                } else {
                    $this->values['mensaje'] = "No se han definido las plantillas.";
                }

                unset($mailer);
                break;
        }

        return parent::IndexAction();
    }

    /**
     * Envía el correo de confirmación al webmaster
     * en base a la plantilla htm $ficheroPlantilla.
     * 
     * @param Mail $mailer objeto mailer
     * @param string $ficheroPlantilla El archivo que tiene la plantilla htm a enviar
     * @return boolean TRUE si se envío con éxito
     */
    private function enviaWebMaster($mailer, $ficheroPlantilla) {

        $plantilla = file_get_contents($ficheroPlantilla);

        $mensaje = "<p>El visitante de la web: <strong>{$this->request['nombre_apellidos']}</strong></p>";
        $mensaje .= "<p>solicita que se le llame al telf: <strong>{$this->request['telefono']}</strong></p>";
        $mensaje .= "<p>en el horario: <strong>{$this->request['horario']}</strong></p>";
        $mensaje .= "<p>Comentarios: <strong>{$this->request['comentarios']}</strong></p>";

        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        return $mailer->send(
                        $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Ha recibido un mensaje en la web', $plantilla, array()
        );
    }

}
