<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE ENLACES DE INTERES
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Enlaces {
    /**
     * Devuelve un array con los enlaces de interes asociados
     * a la entidad $entidad e identidad $identidad
     * 
     * @param string $entidad El nombre de la entidad
     * @param int $idEntidad EL id de la entidad
     * @param int $nItems El número máximo de enlaces a devolver. Opcional (por defecto todos)
     * @return array El array de enlaces de interes
     * 
     * @example Para obtener los primeros 5 enlaces relacionados
     * del contenido cuyo id es 6: getEnlacesRelacionados('GonContenidos',6,5)
     */
    static function getEnlacesRelacionados($entidad, $idEntidad, $nItems = 999999) {

        if ($nItems <= 0)
            $nItems = 999999;

        $objeto = new $entidad($idEntidad);
        $seccionEnlace = $objeto->getIDSeccionEnlaces();
        unset($objeto);

        $array = self::getEnlacesDeInteres($seccionEnlace->getId(), $nItems);

        unset($seccionEnlace);

        return $array;
    }

    /**
     * Devuelve un array con los enlaces de interes de la
     * seccion de enlaces $idSeccion, con un máximo de $nItems enlaces
     * 
     * El array tiene dos elementos:
     * 
     * - seccion: array()
     * - enlaces: array()
     * 
     * Cada elemento del subarray 'seccion' es de la forma:
     *
     * - titulo: el titulo del enlace
     * - subtitulo: el subtitulo del enlace
     * - reusmen: el resumen del enlace
     * - url: array ('url'=>, targetBlank=> boolean)
     *  
     * Cada elemento del subarray 'enlaces' es de la forma:
     * 
     * - titulo: el titulo del enlace
     * - subtitulo: el subtitulo del enlace
     * - resumen: el resumen del enlace
     * - url: array ('url'=>, targetBlank=> boolean)
     * 
     * @param int $idSeccion El id de la seccion de enlaces de interes
     * @param int $nItems El número máximo de enlaces a devolver
     * @return array El array de enlaces de interes
     */
    static function getEnlacesDeInteres($idSeccion, $nItems = 999999) {

        $array = array();

        if ($nItems <= 0)
            $nItems = 999999;

        $array['seccion'] = self::getSecciondeEnlaces($idSeccion);

        $enlace = new EnlEnlaces();
        $rows = $enlace->cargaCondicion("Id", "IdSeccion='{$idSeccion}'", "SortOrder ASC LIMIT {$nItems}");

        foreach ($rows as $row) {
            $enlace = new EnlEnlaces($row['Id']);
            $array['enlaces'][] = array(
                'titulo' => $enlace->getTitulo(),
                'subtitulo' => $enlace->getSubtitulo(),
                'resumen' => $enlace->getResumen(),
                'url' => $enlace->getHref(),
            );
        }
        unset($enlace);

        return $array;
    }

    /**
     * Devuelve un array con las secciones de enlaces de interes con
     * un máximo de $nItems secciones
     * 
     * Cada elemento del array es de la forma:
     * 
     * - titulo: el titulo de la seccion
     * - subtitulo: el subtitulo de la seccion
     * - resumen: el resumen de la seccion
     * - url: array ('url'=>, targetBlank=> boolean)
     *  
     * @param int $nItems Número máximo de secciones a devolver
     * @return array Array de secciones de enlaces
     */
    static function getSeccionesDeEnlaces($nItems = 999999) {

        $array = array();

        if ($nItems <= 0)
            $nItems = 999999;

        $seccion = new EnlSecciones();
        $rows = $seccion->cargaCondicion("Id", '', "SortOrder ASC LIMIT {$nItems}");
        unset($seccion);

        foreach ($rows as $row)
            $array[] = self::getSecciondeEnlaces($row['Id']);

        return $array;
    }

    static function getSecciondeEnlaces($idSeccion) {

        $seccion = new EnlSecciones($idSeccion);
        $array = array(
            'titulo' => $seccion->getTitulo(),
            'subtitulo' => $seccion->getSubtitulo(),
            'resumen' => $seccion->getResumen(),
            'url' => $seccion->getHref(),
        );
        unset($seccion);

        return $array;
    }

}

?>
