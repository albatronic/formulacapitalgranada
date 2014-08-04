<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 10.12.2012 17:38:34
 */

/**
 * @orm:Entity(SldSliders)
 */
class SldSliders extends SldSlidersEntity {

    public function __toString() {
        return $this->Titulo;
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'Titulo';
        return parent::fetchAll($column, $default);
    }

    /**
     * Antes de insertar hay que comprobar que no se exceda
     * el número máximo de banners permitido por zona
     */
    public function validaLogico() {
        parent::validaLogico();

        if ($this->getPrimaryKeyValue() == '') {
            // Voy a insertar
            $zona = new SldZonas($this->IdZona);
            $slider = new SldSliders();
            $rows = $slider->cargaCondicion("count(Id) as nMax", "IdZona='{$this->IdZona}'");
            if ($zona->getNumeroMaximoSliders() < ($rows[0]['nMax'] + 1))
                $this->_errores[] = "Se ha superado el número de sliders para la zona {$zona->getTitulo()}. Consulte con el administrador de la web";
            unset($slider);
            unset($zona);
        }

        $filtro = ($this->getPrimaryKeyValue() == '') ? '1' : "Id<>'{$this->getPrimaryKeyValue()}'";

        $sld = new SldSliders();
        $rows = $sld->cargaCondicion("distinct IdTipo", $filtro);
        unset($sld);

        foreach ($rows as $row)
            $tiposExistentes[$row['IdTipo']] = true;

        // Comprobar la jerarquía de los sliders
        $query = "";

        if ($this->IdTipo > '0') {
            if ($tiposExistentes['1'])
                $this->_errores[] = "Ya existe un slider fijo para toda la WEB";
            else {
                if ($this->IdTipo == '1') {
                    $this->_alertas[] = "Los posibles sliders fijos que hubiese pasan a ser variables";
                    $query = "UPDATE {$this->getDataBaseName()}.SldSliders SET IdTipo='0' WHERE IdTipo>'0'";
                }
                if (($this->IdTipo == '2') and ($tiposExistentes['2'])) {
                    $query = "UPDATE {$this->getDataBaseName()}.SldSliders SET IdTipo='0' WHERE IdTipo='{$this->IdTipo}'";
                    $this->_alertas[] = "Ya existía un slider fijo para el HOME, ha sido cambiado";
                }
                if (($this->IdTipo == '3') and ($tiposExistentes['3'])) {
                    $query = "UPDATE {$this->getDataBaseName()}.SldSliders SET IdTipo='0' WHERE IdTipo='{$this->IdTipo}'";
                    $this->_alertas[] = "Ya existía un slider fijo para la WEB excepto el HOME, ha sido cambiado";
                }

                if ($query) {
                    $em = new EntityManager($this->getConectionName());
                    if ($em->getDbLink()) {
                        $em->query($query);
                        echo $query;
                    }
                    $em->desConecta();
                    unset($em);
                }
            }
        }
    }

    /**
     * Devuelve el objeto enlazado o nulo si no existe enlace
     * 
     * @return \Entidad|null Objeto enlazado
     */
    public function getObjetoEnlazado() {

        if (class_exists($this->Entidad))
            return new $this->Entidad($this->IdEntidad);
        else
            return null;
    }

    /**
     * Devuelve un array con los elemementos necesarios para
     * construir un <a href=''> 
     * 
     * Si el slider está enlazado con contra entidad, se tomará la url de dicha entidad
     * En caso contrario se tomará (si existe) la UrlTarget del slider
     * 
     * Tiene dos elmentos:
     * 
     * - url => Es la url en si con el prefijo, que puede ser: nada, http, o https)
     * - targetBlank => Es un flag booleano para saber si el enlace se abrirá en popup o no
     * 
     * @return array Array
     */
    public function getHref() {

        $array = array();

        // Comprobar si el slider está enlazado con otra entidad
        if ($this->Entidad) {
            $objetoEnlazado = $this->getObjetoEnlazado();
            $array = $objetoEnlazado->getHref();
            unset($objetoEnlazado);
        } else {

            $url = $this->getUrlTarget();

            if ($url) {
                $prefijo = ($this->UrlIsHttps) ? "https://" : "http://";
                $url = $prefijo . $url . $this->getUrlParameters();
                $array = array('url' => $url, 'targetBlank' => $this->UrlTargetBlank);
            }
        }

        return $array;
    }

}

?>