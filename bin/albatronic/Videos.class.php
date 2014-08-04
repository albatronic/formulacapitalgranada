<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE VIDEOS EXTERNOS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Videos {
    
    /**
     * Devuelve un array con videos externos.
     * 
     * El array tiene los siguientes elementos:
     * 
     * - titulo
     * - subtitulo
     * - resumen
     * - autor
     * - tipoVideo: la descripción del tipo (youtube, vimeo, etc)
     * - imagen: el path a la imagen (caratula del video)
     * - urlVideo: el id del video
     * - url: array(url => url, targetBlank => boolean)
     * - ancho: el ancho del video (varWeb GconContenidos [especificas][AnchoVideos])
     * - alto: el alto del video (varWeb GconContenidos [especificas][AltoVideos])
     * 
     * @param int $idSeccion El id de la seccion de videos. Si es <= 0 se muestran todas las secciones.
     * @param int $mostrarEnPortada Menor a 0 para todos, 0 para los NO portada, 1 para los SI portada
     * @param int $nItems Número máximo de videos a devolver. Por defecto todos.
     * @return array Array con los videos
     */
    static function getVideos($idSeccion = 1, $mostrarEnPortada = 0, $nItems = 999999) {

        $filtroSeccion = ($idSeccion <= 0) ? "(1)" : "(IdSeccion='{$idSeccion}')";

        if ($mostrarEnPortada < 0)
            $filtroPortada = "(1)";
        else
            $filtroPortada = ($mostrarEnPortada == 0) ? "(MostrarEnPortada='0')" : "(MostrarEnPortada='1')";

        if ($nItems <= 0)
            $nItems = 999999;

        $filtro = "{$filtroSeccion} AND {$filtroPortada}";
        $orden = ($mostrarEnPortada > 0) ? "OrdenPortada ASC" : "SortOrder ASC";

        $video = new VidVideos();
        $rows = $video->cargaCondicion("Id", $filtro, $orden . " LIMIT {$nItems}");
        unset($video);

        $videos = array();

        $variables = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        
        foreach ($rows as $row) {
            $video = new VidVideos($row['Id']);

            $videos[] = array(
                'titulo' => $video->getTitulo(),
                'subtitulo' => $video->getSubtitulo(),
                'resumen' => $video->getResumen(),
                'autor' => $video->getAutor(),
                'tipoVideo' => $video->getIdTipo()->getDescripcion(),
                'imagen' => $video->getPathNameImagenN(1),
                'urlVideo' => $video->getUrlVideo(),
                'url' => $video->getHref(),
                'ancho' => $variables['especificas']['AnchoVideos'],
                'alto' => $variables['especificas']['AltoVideos'],
                'borde' => $variables['especificas']['AnchoBordeVideos'],
            );
        }

        unset($video);

        return $videos;
    }
}

?>
