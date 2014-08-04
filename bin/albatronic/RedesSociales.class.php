<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE REDES SOCIALES
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática Albatronic, sl
 * @version 1.0 27-04-2013
 */
class RedesSociales {
    
    /**
     * Devuelve un array con los parametros que definen una red social
     * 
     * Cada ocurrencia del array tiene los siguientes elementos:
     * 
     * - titulo : el titulo de la red social
     * - idUsuario: el id o login de la red social
     * - url: la url
     * - numeroItems: número de tweets/caras a mostrar
     * - mostrarAvatar: Booleano, mostrar o no el avatar
     * - mensaje: El mensaje para el caso que no haya tweets a mostrar
     * - botonEnviar: Booleano, mostrar o no el boton eviar
     * - modoMostar:
     * - font
     * - class
     * - action
     * - ancho
     * - alto
     * - size
     * - colorFondo
     * - colorBorde
     * - count
     * - imagen: path a la imagen de diseño 1
     * 
     * @param string $titulo El titulo de la red social por la que filtrar
     * @return array Array con la informacion de la red
     */
    static function getRedSocialByTitulo($titulo) {

        $redes = new Networking();
        $red = $redes->find("Titulo", $titulo);
        unset($redes);

        $array = array(
            'titulo' => $red->getTitulo(),
            'idUsuario' => $red->getIdUsuario(),
            'url' => $red->getUrl(),
            'numeroItems' => $red->getNumeroItems(),
            'mostrarAvatar' => $red->getMostrarAvatar()->getIdTipo(),
            'mensaje' => $red->getMensaje(),
            'botonEnviar' => $red->getBotonEnviar()->getIdTipo(),
            'modoMostar' => $red->getModoMostrar(),
            'font' => $red->getFont(),
            'class' => $red->getClass(),
            'action' => $red->getAction(),
            'ancho' => $red->getAncho(),
            'alto' => $red->getAlto(),
            'size' => $red->getSize(),
            'colorFondo' => $red->getColorFondo(),
            'colorBorde' => $red->getColorBorde(),
            'count' => $red->getCount(),
            'imagen' => $red->getPathNameImagenN(1),
        );


        unset($red);

        return $array;
    }

    static function getRedSocialById($id) {
        
        $red = new Networking($id);

        $array = array(
            'titulo' => $red->getTitulo(),
            'idUsuario' => $red->getIdUsuario(),
            'url' => $red->getUrl(),
            'numeroItems' => $red->getNumeroItems(),
            'mostrarAvatar' => $red->getMostrarAvatar()->getIdTipo(),
            'mensaje' => $red->getMensaje(),
            'botonEnviar' => $red->getBotonEnviar()->getIdTipo(),
            'modoMostar' => $red->getModoMostrar(),
            'font' => $red->getFont(),
            'class' => $red->getClass(),
            'action' => $red->getAction(),
            'ancho' => $red->getAncho(),
            'alto' => $red->getAlto(),
            'size' => $red->getSize(),
            'colorFondo' => $red->getColorFondo(),
            'colorBorde' => $red->getColorBorde(),
            'count' => $red->getCount(),
            'imagen' => $red->getPathNameImagenN(1),
        );


        unset($red);

        return $array;        
    }
    
    /**
     * Devuelve en array con todas las redes sociales
     * 
     * @return array
     */
    static function getRedes() {
        
        $array = array();
        
        $redes = new Networking();
        $rows = $redes->cargaCondicion("Id");
        unset($redes);
        
        foreach ($rows as $row)
            $array[] = self::getRedSocialById($row['Id']);
        
        return $array;
    }
}

?>
