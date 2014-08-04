<?php

/**
 * CLASE PARA PAGINAR
 * 
 * En base a los parámetros obtiene el subconjunto de datos correspondiente a la página $nPagina. 
 * 
 * Con los métodos get se obtiene la información elaborada.
 * 
 * 
 * @param string $entidad La entidad de datos a paginar
 * @param string $condicion Clausula where para filtrar los datos (sin el WHERE)
 * @param string $criterioOrden Criterio de ordenación de los datos (sin el ORDER BY)
 * @param int $nPagina Número de página a devolver
 * @param int $itemsPorPagina Número de items por página
 * @return array Array con la información obtenida
 * 
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) INFORMÁTICA ALBATRONIC, sl
 * @version 1.0 22-ENE-2013
 */
class Paginacion {

    static $rows;
    static $pagina;
    static $totalPaginas;
    static $itemsPorPagina;
    static $totalItems;

    static function paginar($entidad, $condicion, $criterioOrden, $nPagina, $itemsPorPagina) {

        if ($criterioOrden == '') {
            $criterioOrden = "SortOrder ASC";
            echo "Paginacion::paginar: NO SE HA DEFINIDO EL CRITERIO DE ORDEN.";
        }

        // Condición de vigencia
        $ahora = date("Y-m-d H:i:s");
        $filtro = "(Deleted='0') AND (Publish='1') AND (ActiveFrom<='{$ahora}') AND ( (ActiveTo>='{$ahora}') or (ActiveTo='0000-00-00 00:00:00') )";

        // Condición de privacidad
        if (!$_SESSION['usuarioWeb']['Id']) {
            $filtro .= " AND ( (Privacy='0') OR (Privacy='2') )";
        } else {
            $idPerfil = $_SESSION['usuarioWeb']['IdPerfil'];
            $filtro .= " AND ( (Privacy='1') OR (Privacy='2') OR LOCATE('{$idPerfil}',AccessProfileListWeb) )";
        }
        // Condición específica
        if ($condicion != '')
            $filtro .= " AND {$condicion}";

        self::$pagina = ($nPagina <= 0) ? 1 : $nPagina;

        $objeto = new $entidad();

        $query = "SELECT {$objeto->getPrimaryKeyName()} FROM {$objeto->getDataBaseName()}.{$objeto->getTableName()} WHERE {$filtro} ORDER BY {$criterioOrden}";

        $em = new EntityManager($objeto->getConectionName());
        if ($em->getDbLink()) {
            $em->query($query);
            self::$totalItems = $em->numRows();
            self::$itemsPorPagina = ($itemsPorPagina <= 0) ? self::$totalItems : $itemsPorPagina;            
            self::$totalPaginas = floor(self::$totalItems / self::$itemsPorPagina);
            if ((self::$totalItems % self::$itemsPorPagina) > 0)
                self::$totalPaginas++;
            $offset = (self::$pagina - 1) * self::$itemsPorPagina;
            self::$rows = $em->fetchResultLimit(self::$itemsPorPagina, $offset);
            $em->desConecta();
        }
        unset($objeto);
        unset($em);
    }

    /**
     * Array con N ocurrencias: ('Id' => el id del objeto obtenido)
     * @return array
     */
    static function getRows() {
        return self::$rows;
    }

    /**
     * Devuelve un array con la información de paginacion.
     * 
     * Los elementos del array son:
     * 
     * - pagina: El número de página devuelta
     * - totalItems: El número total de items que cumplen el criterio de filtro
     * - itemsPorPagina: El número de items que van en cada página
     * - totalPaginas: El número total de páginas que se devolverían
     * 
     * @return array Array con los datos de paginacion
     */
    static function getPaginacion() {
        return array(
            'pagina' => self::getPagina(),
            'totalItems' => self::getTotalItems(),
            'itemsPorPagina' => self::getItemsPorPagina(),
            'totalPaginas' => self::getTotalPaginas(),
        );
    }

    /**
     * Devuelve el número de página solicitado
     * @return int El número de página
     */
    static function getPagina() {
        return self::$pagina;
    }

    /**
     * Devuelve el número total de páginas
     * @return int 
     */
    static function getTotalPaginas() {
        return self::$totalPaginas;
    }

    /**
     * Devuelve el número de registros por página
     * @return int
     */
    static function getItemsPorPagina() {
        return self::$itemsPorPagina;
    }

    /**
     * Devuelve el número total de items que cumplen el criterio de filtro
     * @return int
     */
    static function getTotalItems() {
        return self::$totalItems;
    }

}

?>
