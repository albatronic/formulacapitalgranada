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
    
    public function getTiposOperacion(){
 
        $array = array();

        $rows = $this->cargaCondicion("distinct(cod_destino) destino", "");
        foreach ($rows as $row) {
            $array[$row['destino']] = $this->_tiposOperacion[$row['destino']];
        }

        return $array;       
    }
    
    public function getTiposInmueble(){
 
        $array = array();

        $rows = $this->cargaCondicion("distinct(des_tipoelem) tipo", "", "des_tipoelem ASC");
        foreach ($rows as $row) {
            $array[$row['tipo']] = $row['tipo'];
        }

        return $array;       
    }

}
