<?php

/**
 * @copyright GRUPO TREVENQUE
 * @date 12.03.2015 23:39:22
 */

/**
 * @orm:Entity(Inmuebles)
 */
class Inmuebles extends InmueblesEntity {

    var $_tiposOperacion = array(
        'V' => 'Venta',
    );

    public function __toString() {
        return ($this->Id) ? $this->Id : '';
    }

    public function getProvinciasPoblacionesZonas() {

        $array = array();

        $rows = $this->cargaCondicion("des_provincia provincia, des_poblacion poblacion, des_zona zona", "", "des_provincia ASC, des_poblacion ASC, des_zona ASC");
        foreach ($rows as $row) {
            $array[$row['provincia']][$row['poblacion']][$row['zona']] = $row['zona'];
        }

        return $array;
    }

    public function getTiposOperacion() {

        $array = array();

        $rows = $this->cargaCondicion("distinct(cod_destino) destino", "");
        foreach ($rows as $row) {
            $array[$row['destino']] = $this->_tiposOperacion[$row['destino']];
        }

        return $array;
    }

    public function getTiposInmueble() {

        $array = array();

        $rows = $this->cargaCondicion("distinct(des_tipoelem) tipo", "", "des_tipoelem ASC");
        foreach ($rows as $row) {
            $array[$row['tipo']] = $row['tipo'];
        }

        return $array;
    }

    public function getSituaciones() {

        $array = array();

        $rows = $this->cargaCondicion("distinct(situacion) situacion", "situacion<>''", "situacion ASC");
        foreach ($rows as $row) {
            $array[$row['situacion']] = $row['situacion'];
        }

        return $array;
    }

    public function save() {

        // Crear la url amigable
        $tipoOperacion = str_replace(" ", "_", strtolower(trim($this->_tiposOperacion[$this->cod_destino])));
        $poblacion = str_replace(" ", "_", strtolower(trim($this->des_poblacion)));
        $tipoInmuble = str_replace(" ", "_", strtolower(trim($this->des_tipoelem)));
        $this->UrlFriendly = "/{$tipoOperacion}_{$poblacion}_{$tipoInmuble}_{$this->cod_elemento}";
        //echo $this->UrlFriendly,"\n";

        $url = new CpanUrlAmigables();
        $url->setIdioma(0);
        $url->setUrlFriendly($this->UrlFriendly);
        $url->setController('Inmobiliaria');
        $url->setAction('Inmueble');
        $url->setTemplate('inmueble.html.twig');
        $url->setEntity('Inmuebles');
        $url->setIdEntity($this->Id);
        $url->create();

        return parent::save();
    }

    public function getBreadcrumb() {
        return $this->getdes_provincia() . " > " . $this->getdes_poblacion() . " > " . $this->getdes_tipoelem();
    }

    public function getFotos() {
        return json_decode($this->Observations);
    }

    public function getRelacionados($nItems = 6, $orden = "Id DESC") {

        $array = array();

        if ($nItems <= 0) {
            $nItems = 5;
        }

        $filtro = "Id<>'{$this->Id}' and (des_poblacion='{$this->des_poblacion}' or des_tipoelem='{$this->des_tipoelem}')";
        
        $rows = $this->cargaCondicion("Id", $filtro, $orden . " limit {$nItems}");
        foreach ($rows as $row) {
            $array[] = new Inmuebles($row['Id']);
        }

        return $array;
    }

}
