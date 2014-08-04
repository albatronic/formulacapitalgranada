<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE MARCAS/FABRICANTES
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 3-jun-2013
 */
class ErpMarcas {

    /**
     * Devuelve array con datos básicos de la marca
     * 
     * @param array Array con datos de la marca
     * @return array
     */
    static function getMarca($row) {

        $url = $row['UrlTarget'];
        $esInterna = ($url == '');

        if ($esInterna) {
            $url = $row['UrlFriendly'];
            $prefijo = $_SESSION['appPath'];
        } else {
            $prefijo = ($row['UrlIsHttps']) ? "https://" : "http://";
        }

        $url = $prefijo . $url . $row['UrlParameters'];

        $href = array('url' => $url, 'targetBlank' => $row['UrlTargetBlank']);

        $array = array(
            'IDFabricante' => $row['IDFabricante'],
            'Titulo' => $row['Titulo'],
            'Descripcion1' => $row['Descripcion1'],
            'Descripcion2' => $row['Descripcion2'],
            'Href' => $href,
        );

        return $array;
    }

    /**
     * Devuelve un objeto marca/fabricante
     * 
     * @param integer $idMarca el id de la marca
     * @return \Fabricantes
     */
    static function getObjetoMarca($idMarca) {
        return new Fabricantes($idMarca);
    }

    /**
     * Devuelve un array de objetos marcas/fabricantes
     * 
     * @param integer $enPortada. 0 No portada, 1 Si portada, 2 Todas. Defecto Si portada
     * @param integer $posicionInicio A partir de que marca. Por defecto desde el principio
     * @param integer $nItems Número de marcas a devolver. Por defecto todas
     * @return array Array de marcas
     */
    static function getMarcas($enPortada = 1, $posicionInicio = 0, $nItems = 0) {

        $array = array();

        $nItems = ($nItems <= 0) ? 999999 : $nItems;
        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $filtro = "";

        if (!in_array($enPortada, array(0, 1, 2)))
            $enPortada = 1;

        switch ($enPortada) {
            case '0':
                $filtro = "MostrarPortada='0'";
                $orden = "SortOrder";
                break;
            case '1':
                $filtro .= "MostrarPortada='1'";
                $orden = "OrdenPortada";
                break;
            case '2':
                $orden = "SortOrder";
                break;
        }

        $fabricantes = new Fabricantes();
        $rows = $fabricantes->cargaCondicion("IDFabricante,Titulo,Descripcion1,Descripcion2,UrlTarget,UrlFriendly,UrlIsHttps,UrlParameters,UrlTargetBlank", $filtro, "{$orden} ASC {$limite}");
        unset($fabricantes);

        foreach ($rows as $row) {
            $array[] = self::getMarca($row);
        }

        return $array;
    }

    static function getObjetosMarcas($enPortada = 1, $posicionInicio = 0, $nItems = 0) {

        $array = array();

        $nItems = ($nItems <= 0) ? 999999 : $nItems;
        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $filtro = "";

        if (!in_array($enPortada, array(0, 1, 2)))
            $enPortada = 1;

        switch ($enPortada) {
            case '0':
                $filtro = "MostrarPortada='0'";
                $orden = "SortOrder";
                break;
            case '1':
                $filtro .= "MostrarPortada='1'";
                $orden = "OrdenPortada";
                break;
            case '2':
                $orden = "SortOrder";
                break;
        }

        $fabricantes = new Fabricantes();
        $rows = $fabricantes->cargaCondicion("IDFabricante", $filtro, "{$orden} ASC {$limite}");
        unset($fabricantes);

        foreach ($rows as $row) {
            $array[] = self::getObjetoMarca($row['IDFabricante']);
        }

        return $array;
    }

    /**
     * Devuelve un array paginado de artículos que pertenecen
     * al fabricante $idFabricante
     * 
     * El array tiene dos elementos:
     * 
     * - articulos => Array de objetos articulos
     * - paginacion => array paginacion
     * 
     * @param integer $idFabricante El id del fabricante
     * @param string $orgen El criterio de orden. Por defecto "SortOrder ASC"
     * @param integer $pagina El número de página
     * @param integer $nItems Número de items por página. Por defecto todos
     * @return array Array de artículos paginado
     */
    static function getArticulosPaginados($idFabricante, $orden, $pagina = 1, $nItems = 0) {

        $nPagina = ($pagina <= 0) ? 1 : $pagina;
        $filtro = "IDFabricante='{$idFabricante}' and Vigente='1'";

        Paginacion::paginar("Articulos", $filtro, $orden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $articulos[] = new Articulos($row['IDArticulo']);

        return array(
            'articulos' => $articulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

}

?>
