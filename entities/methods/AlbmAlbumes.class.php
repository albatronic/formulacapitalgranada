<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 25.12.2012 20:39:54
 */

/**
 * @orm:Entity(AlbmAlbumes)
 */
class AlbmAlbumes extends AlbmAlbumesEntity {

    public function __toString() {
        return $this->Titulo;
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'Titulo';
        return parent::fetchAll($column, $default);
    }

    /**
     * Pone el orden 'OrdenPortada'
     */
    public function validaLogico() {

        parent::validaLogico();

        if (count($this->_errores) == 0)
            $this->OrdenPortada = $this->SortOrder;
    }

}

?>