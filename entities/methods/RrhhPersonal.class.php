<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 12.03.2013 21:52:04
 */

/**
 * @orm:Entity(RrhhPersonal)
 */
class RrhhPersonal extends RrhhPersonalEntity {

    public function __toString() {
        return $this->getTitulo();
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'CONCAT(Apellidos," ",Nombre)';
        return parent::fetchAll($column, $default);
    }

    /**
     * Devuelve un array anidado de departamentos
     * 
     * @return array Array
     */
    public function getArbolHijos($conPersonas = true, $entidadRelacionada = '', $idEntidadRelacionada = '') {

        if ($conPersonas == '') $conPersonas = true;
        
        $dptos = new RrhhDptos();
        $arbolDptos = $dptos->getArbolHijos($conPersonas, $entidadRelacionada, $idEntidadRelacionada);
        unset($dptos);

        return $arbolDptos;
    }

}

?>