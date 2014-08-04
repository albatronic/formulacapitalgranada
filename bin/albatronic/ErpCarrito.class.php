<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ErpCarrito {

    static $errores;
    static $alertas;

    /**
     * Devuelve array con dos elementos:
     * 
     * lineas => array de objetos líneas de carrito
     * totales => array('Unidades,'Importe')
     * 
     * @return array
     */
    static function getCarrito() {

        $carrito = new Carrito();

        $filtro = "sesion='{$_SESSION['IdSesion']}'";
        $rows = $carrito->cargaCondicion("Id", $filtro, "Id ASC");
        foreach ($rows as $row) {
            $lineas[] = self::getLinea($row['Id']);
        }
        unset($carrito);

        return array("lineas" => $lineas, "totales" => self::getTotales());
    }

    /**
     * Devuele el objeto linea del carrito en curso cuyo id es $idLinea
     * 
     * @param integer $idLinea
     * @return \Carrito
     */
    static function getLinea($idLinea) {
        return new Carrito($idLinea);
    }

    /**
     * Añade o incrementa un artículo al carrito asociado
     * a la sesión en curso
     * 
     * @param integer $idArticulo El id del artículo
     * @param integer $unidades Las unidades de producto
     * @return integer El id de la línea creada
     */
    static function addProduct($idArticulo, $unidades = 1) {

        $unidades = ($unidades < 1) ? 1 : $unidades;

        $filtro = "sesion='{$_SESSION['IdSesion']}' and IDArticulo='{$idArticulo}'";
        $carrito = new Carrito();
        $rows = $carrito->cargaCondicion("Id", $filtro);
        if ($rows[0]['Id']) {
            $carrito = new Carrito($rows[0]['Id']);
            $carrito->setUnidades($carrito->getUnidades() + $unidades);
            $carrito->setImporte($carrito->getUnidades() * $carrito->getPrecio() * (1 - $carrito->getDescuento() / 100));
            $id = ($carrito->save()) ? $rows[0]['Id'] : 0;
            self::$errores = $carrito->getErrores();
            self::$alertas = $carrito->getAlertas();
        } else {
            $articulo = new Articulos($idArticulo);
            $carrito->setsesion($_SESSION['IdSesion']);
            $carrito->setIpOrigen($_SERVER['REMOTE_ADDR']);
            $carrito->setUserAgent($_SERVER['HTTP_USER_AGENT']);
            $carrito->setIDArticulo($idArticulo);
            $carrito->setDescripcion($articulo->getDescripcion());
            $carrito->setUnidades($unidades);
            $carrito->setUnidadMedida($articulo->getUnidadMedida("UMV"));
            $carrito->setPrecio($articulo->getPrecioVentaConImpuestos());
            $carrito->setDescuento(0);
            $carrito->setImporte($carrito->getUnidades() * $carrito->getPrecio() * (1 - $carrito->getDescuento() / 100));
            $carrito->setIva($articulo->getIDIva()->getIva());
            $carrito->setRecargo($articulo->getIDIva()->getRecargo());
            $carrito->setEstado(0);
            $id = $carrito->create();
            self::$errores = $carrito->getErrores();
            self::$alertas = $carrito->getAlertas();
            unset($articulo);
        }
        unset($carrito);

        return $id;
    }

    /**
     * Borrar una línea del carrito de la sesion en curso
     * 
     * @param integer $idLinea El id de la linea
     * @return boolean True se el borrado ha sido ok
     */
    static function removeProduct($idLinea) {

        $carrito = new Carrito();
        $filtro = "sesion='{$_SESSION['IdSesion']}' and Id='{$idLinea}'";
        $ok = ($carrito->queryDelete($filtro) > 0);
        unset($carrito);

        return $ok;
    }

    /**
     * Actualiza la línea $idLinea del carrito en curso con las
     * unidades $unidades de producto, actualizando también el importe de la línea
     * 
     * @param integer $idLinea El id de la línea a actualizar
     * @param decimal $unidades El número de unidades de producto a actulizar
     * @return integer El id de la línea actualizada
     */
    static function updateProduct($idLinea, $unidades) {

        $carrito = new Carrito($idLinea);
        $idLinea = $carrito->getId();
        if ($idLinea) {
            $carrito->setUnidades($unidades);
            $carrito->setImporte($carrito->getPrecio() * $unidades * (1 - $carrito->getDescuento() / 100));
            $carrito->save();
            self::$errores = $carrito->getErrores();
            self::$alertas = $carrito->getAlertas();
        }

        return $idLinea;
    }

    /**
     * Devuelve array con los totales del carrito
     * de la sesion en curso.
     * 
     * El array tiene dos elementos: Unidades, Importe
     * 
     * @return array Array con los totales
     */
    static function getTotales() {

        $carrito = new Carrito();
        $filtro = "sesion='{$_SESSION['IdSesion']}'";
        $rows = $carrito->cargaCondicion("sum(Unidades) as Unidades, sum(Importe) as Importe", $filtro);

        return $rows[0];
    }

    static function getErrores() {
        return self::$errores;
    }

    static function getAlertas() {
        return self::$alertas;
    }

}
