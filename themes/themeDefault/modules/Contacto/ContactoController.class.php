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

    public function IndexAction() {

        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);

        switch ($this->request['METHOD']) {
            case 'GET':
                $this->values['accion'] = "";
                if ($this->request[1] == "log") {
                    return $this->Log();
                } else {
                    mail($this->varWeb['Pro']['mail']['from'], "Credit Granada", "Han entrado en el formulario de contacto general");
                    $fp = fopen("log/contacto.log", "a");
                    fwrite($fp, date("Y-m-d H:i:s") . ";" . $_SERVER['REMOTE_ADDR'] . ";Entran en contacto principal" . "\n");
                    fclose($fp);
                }
                break;

            case 'POST':

                $this->values['accion'] = "envio";

                if (file_exists('docs/plantillaMailContacto.htm')) {

                    $mailer = new Mail($this->varWeb['Pro']['mail']);
                    $envioOk = $this->enviaVisitante($mailer, 'docs/plantillaMailContacto.htm');

                    if ($envioOk) {
                        $envioOk = $this->enviaWebMaster($mailer, 'docs/plantillaMailContacto.htm');
                    }

                    $this->values['mensaje'] = ($envioOk) ?
                            $this->varWeb['Pro']['mail']['mensajeExito'] :
                            $this->varWeb['Pro']['mail']['mensajeError'];

                    unset($mailer);
                } else {
                    $this->values['mensaje'] = "No se han definido las plantillas.";
                }

                break;
        }

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
    private function enviaVisitante($mailer, $ficheroPlantilla) {

        $plantilla = file_get_contents($ficheroPlantilla);
        $plantilla = str_replace("#MENSAJE#", $this->varWeb['Pro']['mail']['mensajeConfirmacion'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);

        return $mailer->send(
                        $this->request['email'], $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Hemos recibido su mensaje', $plantilla, array()
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
    private function enviaWebMaster($mailer, $ficheroPlantilla) {

        $plantilla = file_get_contents($ficheroPlantilla);

        $mensaje = "<p style='margin-bottom: 20px;'><strong>Han dejado el siguiente mensaje en el formulario de contacto:</strong></p>";
        $mensaje .= "<p>Nombre: {$this->request['nombre']}</p>";
        $mensaje .= "<p>Apellidos: {$this->request['apellidos']}</p>";
        $mensaje .= "<p>Email: {$this->request['email']}</p>";
        $mensaje .= "<p>Tel&eacute;fono: {$this->request['telefono']}</p>";
        $mensaje .= "<p>Domicilio: {$this->request['domicilio']}</p>";
        $mensaje .= "<p>Provincia: {$this->request['provincia']}</p>";
        $mensaje .= "<p>Ciudad: {$this->request['ciudad']}</p>";
        $mensaje .= "<p>Solucion: {$this->request['solucion']}</p>";
        $mensaje .= "<p>Importe: {$this->request['importe']}</p>";
        $mensaje .= "<p>Observaciones: {$this->request['observaciones']}</p>";

        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        return $mailer->send(
                        $this->varWeb['Pro']['mail']['from'], $this->request['email'], $this->request['nombre'], 'Ha recibido un mensaje en la web', $plantilla, array()
        );
    }

    public function Log() {

        $log = "log/contacto.log";
        if (file_exists($log)) {
            $fp = fopen($log, "r");
            $this->values['csv'] = fread($fp, filesize($log));
            fclose($fp);
        }

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=contacto.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        return array(
            'template' => $this->entity . "/log.csv.twig",
            'values' => $this->values,
        );
    }

}
