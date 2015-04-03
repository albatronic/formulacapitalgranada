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
    
    var $ordenes = array(
        'precio ASC' => 'Precio a-z',
        'precio DESC' => 'Precio z-a',
        'numhabitac ASC' => 'Habitacines a-z',
        'numhabitac DESC' => 'Habitaciones z-a',
    );

    public function IndexAction() {

        $this->values['menuLateral'] = Menu::getMenuN(2);
        $this->values['contenido'] = new GconSecciones($this->request['IdEntity']);

        $inmuebles = new Inmuebles();

        $this->values['filtro'] = array(
            'provPobZon' => $inmuebles->getProvinciasPoblacionesZonas(),
            'tiposOperacion' => $inmuebles->getTiposOperacion(),
            'tiposInmueble' => $inmuebles->getTiposInmueble(),
            'situacion' => $inmuebles->getSituaciones(),
            'ordenes' => $this->ordenes,
        );
        $this->values['provPobZon'] = json_encode($this->values['filtro']['provPobZon']);

        switch ($this->request['METHOD']) {
            case 'GET':
                $filtro = "";
                $pagina = 1;
                $orden = "Id DESC";
                $valoresFiltro = array();
                break;
            case 'POST':
                $pagina = ($this->request['pagina'] != '') ? $this->request['pagina'] : 1;
                $valoresFiltro = $this->request['filtro'];
                $filtroProvincia = ($valoresFiltro['provincia'] != '') ? "(des_provincia='{$valoresFiltro['provincia']}')" : "(1)";
                $filtroPoblacion = ($valoresFiltro['poblacion'] != '') ? "(des_poblacion='{$valoresFiltro['poblacion']}')" : "(1)";
                $filtroZona = ($valoresFiltro['zona'] != '') ? "(des_zona='{$valoresFiltro['zona']}')" : "(1)";
                $filtroTipoOperacion = ($valoresFiltro['tipoOperacion'] != '') ? "(cod_destino='{$valoresFiltro['tipoOperacion']}')" : "(1)";
                $filtroTipoInmueble = ($valoresFiltro['tipoInmueble'] != '') ? "(des_tipoelem='{$valoresFiltro['tipoInmueble']}')" : "(1)";
                $filtroSituacion = ($valoresFiltro['situacion'] != '') ? "(situacion='{$valoresFiltro['situacion']}')" : "(1)";

                $filtro = "{$filtroProvincia} AND {$filtroPoblacion} AND {$filtroZona} AND {$filtroTipoOperacion} AND {$filtroTipoInmueble} AND {$filtroSituacion}";
                $orden = $valoresFiltro['orden'];
                break;
        }

        Paginacion::paginar("Inmuebles", $filtro, $orden, $pagina, 10);
        foreach (Paginacion::getRows() as $row) {
            $inmueble = new Inmuebles($row['Id']);
            ;
            $this->values['inmuebles'][] = $inmueble;
        }
        $this->values['paginacion'] = Paginacion::getPaginacion();
        $this->values['seleccion'] = $valoresFiltro;

        $template = ($this->request['Template'] !== '') ? $this->entity . "/" . $this->request['Template'] :
                $this->controller . "/index.html.twig";

        return array(
            'template' => $template,
            'values' => $this->values,
        );
    }

    public function InmuebleAction() {

        $inmueble = new Inmuebles($this->request['IdEntity']);
        $this->values['inmueble'] = $inmueble;
        $this->values['ruta'][] = array('nombre' => "Inmobiliaria", "url" => array("url" => "Inmobiliaria"),);
        $this->values['ruta'][] = array('nombre' => $inmueble->getBreadcrumb() , );    

        return array(
            "template" => $this->entity . "/inmueble.html.twig",
            "values" => $this->values,
        );
    }

}
