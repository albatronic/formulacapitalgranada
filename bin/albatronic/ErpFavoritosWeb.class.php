<?php

/**
 * Gestión de  ErpFavoritosWeb
 *
 * @author Informatica ALBATRONIC, SL <sergio.perez@albatronic.com>
 * @since 11-mar-2014
 */
class ErpFavoritosWeb {

    /**
     * Añade a los favoritos web del usuario en curso el
     * artículo $idArticulo
     * 
     * @param integer $idArticulo El id de artículo
     */
    static function add($idArticulo) {

        $idUsuario = $_SESSION['usuarioWeb']['Id'];

        $favorito = new FavoritosWeb();
        $rows = $favorito->cargaCondicion("Id", "IDUsuario='{$idUsuario}' and IDArticulo='{$idArticulo}'");
        if (count($rows) == 0) {
            $favorito->setIDUsuario($idUsuario);
            $favorito->setIDArticulo($idArticulo);
            $favorito->create();
        }
    }

    /**
     * Quita de los favoritos web del usuario en curso el
     * artículo $idArticulo
     * 
     * @param integer $idArticulo El id de artículo
     */
    static function remove($idArticulo) {

        $idUsuario = $_SESSION['usuarioWeb']['Id'];

        $favorito = new FavoritosWeb();
        $favorito->queryDelete("IDUsuario='{$idUsuario}' AND IDArticulo='{$idArticulo}'");
        unset($favorito);
    }

    /**
     * Devuelve un array con objetos artículos que son favoritos
     * del usuario web en curso
     * 
     * @return \Articulos Array de objetos articulos
     */
    static function getFavoritos() {

        $idUsuario = $_SESSION['usuarioWeb']['Id'];

        $favorito = new FavoritosWeb();
        $rows = $favorito->cargaCondicion("IDArticulo, Checked", "IDUsuario='{$idUsuario}'", "CreatedAt DESC");
        unset($favorito);

        $array = array();

        foreach ($rows as $row) {
            $array[] = array(
                "articulo" => new Articulos($row['IDArticulo']),
                "Checked" => $row['Checked']
            );
        }

        return $array;
    }

    /**
     * Pone en el carrito todos los articulos
     * que son favoritos del usuario en curso y
     * están marcados para envio al carrito
     * 
     * Los artículos que ya existen en el carrito
     * serán incrementados en 1 unidad
     */
    static function addAlCarrito() {

        $idUsuario = $_SESSION['usuarioWeb']['Id'];

        $favorito = new FavoritosWeb();
        $rows = $favorito->cargaCondicion("IDArticulo", "IDUsuario='{$idUsuario}' AND Checked='1'");
        unset($favorito);

        foreach ($rows as $row) {
            ErpCarrito::addProduct($row['IDArticulo']);
        }
    }

    /**
     * Marca todos los favoritos del usuario en curso
     * para pasar al carrito
     * 
     * @return integer El número de favoritos marcados
     */
    static function checkAll() {
        $objeto = new FavoritosWeb();
        $filtro = "IDUsuario='{$_SESSION['usuarioWeb']['Id']}'";
        $resultado = $objeto->queryUpdate(array("Checked" => "1"), $filtro);
        unset($objeto);

        return $resultado;
    }

    /**
     * Desmarca todos los favoritos del usuario en curso
     * para pasar al carrito
     * 
     * @return integer El número de favoritos desmarcados
     */
    static function unCheckAll() {
        $objeto = new FavoritosWeb();
        $filtro = "IDUsuario='{$_SESSION['usuarioWeb']['Id']}'";
        $resultado = $objeto->queryUpdate(array("Checked" => "0"), $filtro);
        unset($objeto);

        return $resultado;
    }

    /**
     * Poner el valor de check a $valor para el favorito $idArticulo
     * del usuario en curso
     * 
     * @param integer $idArticulo
     * @param integer $valor
     */
    static function check($idArticulo, $valor) {
        $objeto = new FavoritosWeb();
        $filtro = "IDArticulo='{$idArticulo}' AND IDUsuario='{$_SESSION['usuarioWeb']['Id']}'";
        $objeto->queryUpdate(array("Checked" => "{$valor}"), $filtro);
        unset($objeto);        
    }
}
