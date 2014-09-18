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
                // Minificha con todos los servicios de una familia
                $this->values['familiaServicio'] = Servicios::getFamilia($this->request['IdEntity']);
                $servicios = Servicios::getServicios($this->request['IdEntity'], -1);
                // hacer parejas de servcios para el template
                $i = -1;
                $array = array();
                foreach ($servicios as $key => $servicio) {
                    if (!($key % 2)) {
                        $i++;
                    }
                    $array[$i][] = $servicio;
                }
                $this->values['servicios'] = $array;
                $template = '/index.html.twig';
        }

        return array(
            'template' => $this->entity . $template,
            'values' => $this->values,
        );
    }

    public function SocilitaInfoAction() {

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

        return array(
            'template' => $this->entity . '/servicio.html.twig',
            'values' => $this->values,
        );
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

        $mensaje = "<p style='margin-bottom: 20px;'><strong>Han dejado el siguiente mensaje en el formulario 'Solicita infromación sin compromiso':</strong></p>";
        $mensaje .= "<p>Para el servicio: {$this->request['servicio']}</p>";
        $mensaje .= "<p>Nombre y apellidos: {$this->request['nombre_apellidos']}</p>";
        $mensaje .= "<p>Tel&eacute;fono: {$this->request['telefono']}</p>";
        $mensaje .= "<p>Email: {$this->request['email']}</p>";
        $mensaje .= "<p>Importe: {$this->request['importe']}</p>";
        $mensaje .= "<p>Domicilio: {$this->request['domicilio']}</p>";
        $mensaje .= "<p>Comentarios: {$this->request['comentarios']}</p>";

        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        return $mailer->send(
                        $this->varWeb['Pro']['mail']['from'], $this->request['email'], $this->request['nombre'], 'Ha recibido un mensaje en la web', $plantilla, array()
        );
    }

}
