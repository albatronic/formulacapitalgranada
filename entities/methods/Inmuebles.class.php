<?php

/**
 * @copyright GRUPO TREVENQUE
 * @date 12.03.2015 23:39:22
 */

/**
 * @orm:Entity(Inmuebles)
 */
class Inmuebles extends InmueblesEntity {

    public function __toString() {
        return ($this->Id) ? $this->Id : '';
    }

}
