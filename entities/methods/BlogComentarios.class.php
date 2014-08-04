<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 21.02.2013 21:32:01
 */

/**
 * @orm:Entity(BlogComentarios)
 */
class BlogComentarios extends BlogComentariosEntity {

    public function __toString() {
        return $this->getId();
    }

}

?>