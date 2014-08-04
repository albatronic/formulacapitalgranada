<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 18.01.2013 11:35:20
 */

/**
 * @orm:Entity(Networking)
 */
class Networking extends NetworkingEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>