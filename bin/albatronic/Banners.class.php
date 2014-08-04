<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE BANNERS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática Albatronic, sl
 * @version 1.0 15-mar-2013
 */
class Banners {
    
    /**
     * Devuelve un array con BANNERS.
     * 
     * Están ordenados ASCEDENTEMENTE por Id u OrdenMostrarEnListado en el caso
     * que se vayan a devolver solo los que sean mostrarEnListado TRUE.
     * 
     * Si el registro de banner existe pero no tiene imagenes, o
     * teniéndolas no están marcadas publicar, no se tendrá en cuenta.
     * 
     * El array tiene 5 elementos:
     * 
     * - titulo => el titulo del banner
     * - subtitulo => el subtitulo del banner
     * - resumen => el resumen del banner
     * - url => array(url => texto, targetBlank => boolean)
     * - imagenes => array con los paths de las imágenes
     * 
     * @param int $zona El nombre de la zona de banner a filtrar. Opcional. Defecto la primera que encuentre.
     * @param int $tipo El tipo de banner. Un valor negativo significa todos los tipos. Por defecto 0 (fijo). Valores posibles en entities/abstract/TiposBanners.class.php
     * @param boolean $mostrarEnListado Un valor negativo para todos, 0 para los NO y 1 para los SI mostrar en listado
     * @param int $nItems Número máximo de banners a devolver. Opcional. Defecto todos
     * @return array Array de banners
     */
    static function getBanners($zona = '*', $tipo = 0, $mostrarEnListado = 0, $nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        // Valido el tipo de banner. Si no es correcto lo pongo a tipo 0 (fijo)
        if ($tipo < 0)
            $filtroTipo = "(1)";
        else {
            $tipoBanner = new TiposBanners($tipo);
            if ($tipoBanner->getIDTipo() == null)
                $tipo = 0;
            $filtroTipo = "(IdTipo='{$tipo}')";
        }

        // Filtro Zona
        $filtroZona = ($zona == '*') ? "(1)" : "(IdZona IN (select Id from BannZonas where Titulo='{$zona}'))";

        // Filtro de 'mostrarEnListado'
        $filtroMostrarEnListado = ($mostrarEnListado < 0) ? "(1)" : "MostrarEnListado='{$mostrarEnListado}'";

        // Criterio de orden
        $orden = ($mostrarEnListado) ? "OrdenMostrarEnListado ASC" : "Id ASC";

        $filtro  = "{$filtroZona} AND {$filtroTipo} AND {$filtroMostrarEnListado}";
        
        $banner = new BannBanners();
        $rows = $banner->cargaCondicion("Id", $filtro, "{$orden} {$limite}");
        unset($banner);

        foreach ($rows as $row) {
            $banner = new BannBanners($row['Id']);
            $documentos = $banner->getDocuments('image%');

            $imagenes = array();
            foreach ($documentos as $documento) {
                $imagenes[] = $documento->getPathName();
            }

            // No se tiene en cuenta los banners que no tienen imagenes
            if (count($imagenes)) {
                $array[] = array(
                    'titulo' => $banner->getTitulo(),
                    'subtitulo' => $banner->getSubtitulo(),
                    'resumen' => $banner->getResumen(),
                    'url' => $banner->getHref(),
                    'imagenes' => $imagenes,
                );
            }
        }
        unset($banner);
        unset($documentos);

        return $array;
    }
}

?>
