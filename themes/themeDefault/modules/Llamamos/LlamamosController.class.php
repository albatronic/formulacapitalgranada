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
                break;

            case 'POST':

                $this->values['accion'] = "envio";

                $mailer = new Mail($this->varWeb['Pro']['mail']);
                //$envioOk = $this->enviaVisitante($mailer, 'docs/plantillaMailVisitante.htm');
                //if ($envioOk) {

                $mensaje  = "<p>El visitante de la web: <strong>{$this->request['nombre_apellidos']}</strong></p>";
                $mensaje .= "<p>solicita que se le llame al telf: <strong>{$this->request['telefono']}</strong></p>";
                $mensaje .= "<p>en el horario: <strong>{$this->request['horario']}</strong></p>";
                $mensaje .= "<p>Comentarios: <strong>{$this->request['comentarios']}</strong></p>";
                $envioOk = $mailer->send(
                        $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from'], "Formulario Quiero que me llamen", 'Solicitud de llamada en la web', $mensaje, array()
                );
                //}

                $this->values['mensaje'] = ($envioOk) ?
                        $this->varWeb['Pro']['mail']['mensajeExito'] :
                        $this->varWeb['Pro']['mail']['mensajeError'];

                unset($mailer);

                break;
        }

        return parent::IndexAction();
    }

}
