<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 17.10.2012 00:16:48
 */

/**
 * @orm:Entity(PcaePermisos)
 */
class PcaePermisos extends PcaePermisosEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>