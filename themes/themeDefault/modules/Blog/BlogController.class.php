<?php

/**
 * Description of BlogController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 15-agosto-2014
 *
 */
class BlogController extends ControllerProject {

    protected $entity = "Blog";

    public function IndexAction() {

        switch ($this->request['METHOD']) {
            case 'GET':
                // Puede ser la sección principal del blog o una categoría
                $seccion = new GconSecciones($this->request['IdEntity']);
                if ($seccion->getBelongsTo()->getId() == 0) {
                    $this->values['blog'] = Blog::getArticulos(0, false, 1, 0, 1);
                } else {
                    $this->values['blog'] = Blog::getArticulos($this->request['IdEntity'], false, 1, 0, 1);
                    $this->values['blog']['categoria'] = $seccion->getTitulo();
                }

                break;
            case 'POST':
                // Puede venir desde el buscador de texto, el archivo de meses o la paginación
                if ($this->request['texto']) {
                    $this->values['blog'] = Blog::getArticulosBusqueda($this->request['texto'], false, $this->request['pagina'], 0, 1);
                    $this->values['blog']['texto'] = $this->request['texto'];
                } elseif ($this->request['anio']) {
                    $anio = $this->request['anio'];
                    $mes = $this->request['mes'];
                    if ($mes < 1 || $mes > 12) {
                        $mes = date('m');
                    }
                    $meses = new Meses($mes);
                    $this->values['blog'] = Blog::getArticulosMes($anio, $mes, false, $this->request['pagina'], 0, 1);
                    $this->values['blog']['anio'] = $anio;
                    $this->values['blog']['mes'] = $mes;
                    $this->values['blog']['textoMes'] = $meses->getDescripcion();
                    unset($meses);
                } else {
                    $this->values['blog'] = Blog::getArticulos(0, false, $this->request['pagina'], 0, 1);
                }
                break;
        }

        $this->values['blog']['categorias'] = Blog::getSecciones();
        //$this->values['postsPorMes'] = Blog::getArticulosMeses(12);
        //$this->values['testimonios'] = Contenidos::getContenidosSeccion($this->varWeb['Pro']['staticContents'][1]);

        return parent::IndexAction();
    }

}