<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE LOS SERVICIOS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Servicios {

    /**
     * Devuelve el objeto servicio cuyo id es $idServicio
     * 
     * @param int $idServicio El id del servicio
     * @return \ServServicios El objeto servicio
     */
    static function getServicio($idServicio) {
        return new ServServicios($idServicio);
    }

    /**
     * Devuelve el objeto Familia de servicio
     * @param integer $idFamilia El id de la familia
     * @return \ServFamilias
     */
    static function getFamilia($idFamilia) {
        return new ServFamilias($idFamilia);
    }
    
    /**
     * Devuelve un array con objetos Servicios
     * 
     * @param int $idFamilia El id de la familia de servicios. Si es <= 0 se muestran todas las familias. Por defecto 0
     * @param int $mostrarEnPortada Menor a 0 para todos, 0 para los NO portada, 1 para los SI portada. Por defecto 0
     * @param int $nItems Número máximo de servicios a devolver. Por defecto todos.
     * @param int $idExcluir El id del servicio a excluir en la lista de servicios devueltos
     * @return array Array con objetos servicios
     */
    static function getServicios($idFamilia = 0, $mostrarEnPortada = 0, $nItems = 999999, $idExcluir = 0) {

        $filtroFamilia = ($idFamilia <= 0) ? "(1)" : "(IdFamilia='{$idFamilia}')";

        if ($mostrarEnPortada < 0) {
            $filtroPortada = "(1)";
        } else {
            $filtroPortada = ($mostrarEnPortada == 0) ? "(MostrarPortada='0')" : "(MostrarPortada='1')";
        }

        if ($nItems <= 0) {
            $nItems = 999999;
        }

        $filtro = "{$filtroFamilia} AND {$filtroPortada}";

        if ($idExcluir > 0) {
            $filtro .= " AND (Id <> '$idExcluir')";
        }

        $orden = ($mostrarEnPortada > 0) ? "MostrarPortadaOrden ASC" : "SortOrder ASC";

        $servicio = new ServServicios();
        $rows = $servicio->cargaCondicion("Id", $filtro, $orden . " LIMIT {$nItems}");
        unset($servicio);

        $servicios = array();

        foreach ($rows as $row) {
            $servicios[] = self::getServicio($row['Id']);
        }

        return $servicios;
    }

    /**
     * Devuelve un array con objetos Servicios
     * 
     * @param int $mostrarEnPortada Menor a 0 para todos, 0 para los NO portada, 1 para los SI portada. Por defecto 0
     * @param int $nItems Número máximo de servicios a devolver. Por defecto todos.
     * @param int $idExcluir El id del servicio a excluir en la lista de servicios devueltos
     * @return array Array con objetos servicios
     */
    static function getServiciosAgrupados($mostrarEnPortada = 0, $nItems = 999999, $idExcluir = 0) {

        if ($mostrarEnPortada < 0) {
            $filtroPortada = "(1)";
        } else {
            $filtroPortada = ($mostrarEnPortada == 0) ? "(MostrarPortada='0')" : "(MostrarPortada='1')";
        }

        if ($nItems <= 0) {
            $nItems = 999999;
        }

        $filtro = "{$filtroPortada}";

        if ($idExcluir > 0) {
            $filtro .= " AND (Id <> '$idExcluir')";
        }

        $orden = ($mostrarEnPortada > 0) ? "MostrarPortadaOrden ASC" : "SortOrder ASC";

        $servicio = new ServServicios();
        $rows = $servicio->cargaCondicion("IdFamilia,Id", $filtro, "IdFamilia ASC,{$orden} LIMIT {$nItems}");
        unset($servicio);

        $servicios = array();

        foreach ($rows as $row) {
            $servicio = self::getServicio($row['Id']);
            if (!isset($servicios[$row['IdFamilia']]['familia'])) {
                $servicios[$row['IdFamilia']]['familia'] = $servicio->getIdFamilia();
            }
            $servicios[$row['IdFamilia']]['servicios'][] = $servicio;
        }

        return $servicios;
    }

    /**
     * Devuelve un array con la información del servicio desarrollado
     * 
     * El array tiene los elementos:
     * 
     * - servicio: El OBJETO servicio
     * - galeriaFotos: Array con la galeria de fotos
     * - enlacesRelacionados: Array de enlaces relacionados
     * - docsAdjuntos: Array de documentos adjuntos
     * - videos: Array de videos
     * 
     * @param int $idServicio El id del servicio
     * @param int $nImagenes Numero de imágenes que van en la galeria de fotos
     * @return array Array con el servicio desarrollado
     */
    static function getServicioDesarrollado($idServicio, $nImagenes = 99999) {

        if ($nImagenes <= 0) {
            $nImagenes = 99999;
        }

        $servicio = self::getServicio($idServicio);
        $idSeccionVideos = $servicio->getIDSeccionVideos()->getId();
        $videos = ($idSeccionVideos > 0) ? Videos::getVideos($idSeccionVideos, -1) : array();

        return array(
            'servicio' => $servicio,
            'galeriaFotos' => Albumes::getAlbumExterno('ServServicios', $idServicio, $nImagenes),
            'enlacesRelacionados' => Enlaces::getEnlacesRelacionados('ServServicios', $idServicio),
            'docsAdjuntos' => $servicio->getDocuments('document'),
            'videos' => $videos,
        );
    }

    /**
     * Devuelve array con objetos Familias de servicios
     * 
     * @param integer $nItems EL número de familias a devolver. Opcional. O=Todas
     * @return array Array de objetos \ServFamilias
     */
    static function getFamilias($nItems=0) {
        
        $limit = ($nItems<=0) ? "" : "limit {$nItems}";
        
        $familias = new ServFamilias();
        $rows = $familias->cargaCondicion("Id","(1) {$limit}");
        unset($familias);
        
        $array = array();
        
        foreach ($rows as $row) {
            $array[] = new ServFamilias($row['Id']);
        }
        
        return $array;
    }
}
