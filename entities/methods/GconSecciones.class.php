<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 06.11.2012 20:33:07
 */

/**
 * @orm:Entity(GconSecciones)
 */
class GconSecciones extends GconSeccionesEntity {

    public function __toString() {
        return $this->getTitulo();
    }

    /**
     * Devuelve un array recursivo con las subsecciones de la sección en curso
     * 
     * Cada elemento del array es:
     * 
     * - seccion: El texto de la etiquetaweb $nMenu
     * - subetiquetaWeb: El texto de la subetiquetaweb $nMenu
     * - url: array(url => La url, targetBlank => boolean)
     * - subsecciones: array recursivo
     * 
     * @param string $orden Criterio de orden. Defecto 'SortOrder'
     * @return array Array de subsecciones
     */
    public function getArraySubsecciones($orden = "SortOrder", $nMenu = '1') {

        $array = array();

        $subseccion = new GconSecciones();
        $filtro = "BelongsTo='{$this->Id}'";
        $rows = $subseccion->cargaCondicion("Id", $filtro, "{$orden} ASC");

        foreach ($rows as $row) {
            $subseccion = new GconSecciones($row['Id']);
            $array[] = array(
                'seccion' => $subseccion->{"getEtiquetaWeb$nMenu"}(),
                'subetiquetaWeb' => $subseccion->{"getSubetiquetaWeb$nMenu"}(),
                'url' => $subseccion->getHref(),                
                'subsecciones' => $subseccion->getArraySubsecciones("OrdenMenu{$nMenu}",$nMenu),
            );
        }
        unset($subseccion);

        return $array;
    }

    /**
     * Devuelve el número de subsecciones hijas de la sección actual
     * No cuenta las eventuales subsecciones nietas, biznietas, etc
     * 
     * Solo se tienen en cuenta las subsecciones que Publish=1
     * 
     * @return int El número de subsecciones directas
     */
    public function getNumberOfSubsecciones() {

        $filtro = "BelongsTo='{$this->Id}'";

        return $this->getNumberOfRecords($filtro);
    }

    /**
     * Devuelve el número de contenidos directos que tiene la sección
     * Solo se tienen en cuenta los que Publish=1
     * 
     * @param string $filtro Filtro adicional
     * @return int El número de contenidos de la seccion
     */
    public function getNumberOfContenidos($filtro = '1') {
        
        $contenido = new GconContenidos();
        $nContenidos = $contenido->getNumberOfRecords("IdSeccion='{$this->Id}' AND ({$filtro})");
        unset($contenido);

        return $nContenidos;
    }

}

?>