<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE CONTENIDOS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Contenidos {

    /**
     * Devuelve el objeto contenido cuyo id es $idContenido, o
     * un array con las columnas indicadas en $arrayColumnas
     * 
     * Este método existe por compatibilidad con otras versiones. Lo correcto
     * es usar el método más genérico 'getObjeto'
     * 
     * @param type $idContenido
     * @param type $arrayColumnas
     * @return type
     */
    static function getContenido($idContenido, $arrayColumnas = '') {
        return ControllerWeb::getObjeto('GconContenidos', $idContenido, $arrayColumnas);
    }

    /**
     * Devuelve un array de contenidos paginados correspondientes a la sección $idSeccion
     * Si no se indica seccion, se devuelven todas
     * 
     * Los contenidos se ordenan ascendentemente por SortOrder
     * 
     * Si no se indica $nItems se paginará según el valor de la variable web
     * del módulo de contenidos ['especificas']['NumContenidosPorPaginaListado']
     * 
     * @param int $idSeccion El id de la seccion de contenidos
     * @param int $nPagina El número de página
     * @param int $nItems EL número de contenidos por página
     * @return array Array de contenidos
     */
    static function getContenidosSeccion($idSeccion = 0, $nPagina = 1, $nItems = 0) {

        $arrayContenidos = array();

        if ($nPagina <= 0)
            $nPagina = 1;

        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0)
            $nItems = $var['especificas']['NumContenidosPorPaginaListado'];

        if ($nItems <= 0)
            $nItems = 5;

        $filtro = ($idSeccion <= 0) ? "(1)" : "IdSeccion='{$idSeccion}'";

        $criterioOrden = $var['especificas']['CriterioOrdenContenidos'];
        if ($criterioOrden == '')
            $criterioOrden = "SortOrder DESC";

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayContenidos[] = self::getContenidoDesarrollado($row['Id']);

        return array(
            'contenidos' => $arrayContenidos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array de contenidos paginados correspondientes a la sección $idSeccion
     * Si no se indica seccion, se devuelven todas
     * 
     * Los contenidos se ordenan ascendentemente por SortOrder
     * 
     * Si no se indica $nItems se paginará según el valor de la variable web
     * del módulo de contenidos ['especificas']['NumContenidosPorPaginaListado']
     * 
     * @param int $idSeccion El id de la seccion de contenidos
     * @param text $texto El texto a buscar en el titulo, subtitulo, resumen y desarrollo
     * @param int $nPagina El número de página
     * @param int $nItems EL número de contenidos por página
     * @return array Array de contenidos
     */
    static function getContenidosBusqueda($idSeccion = 0, $texto = '', $nPagina = 1, $nItems = 0) {

        $arrayContenidos = array();

        if ($nPagina <= 0)
            $nPagina = 1;

        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0)
            $nItems = $var['especificas']['NumContenidosPorPaginaListado'];

        if ($nItems <= 0)
            $nItems = 5;

        $filtro = ($idSeccion <= 0) ? "(1)" : "IdSeccion='{$idSeccion}'";
        $filtro .= " and (Titulo like '%{$texto}%' or Subtitulo like '%{$texto}%' or Resumen like '%{$texto}%' or Desarrollo like '%{$texto}%')";

        $criterioOrden = "SortOrder DESC";

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayContenidos[] = self::getContenidoDesarrollado($row['Id']);

        return array(
            'contenidos' => $arrayContenidos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array con la información del contenido desarrollado
     * 
     * El array tiene los elementos:
     * 
     * - contenido: El OBJETO contenido
     * - comentarios: Array de objeto BlogComentarios
     * - galeriaFotos: Array con la galeria de fotos
     * - enlacesRelacionados: Array de enlaces relacionados
     * - docsAdjuntos:
     * - videos:
     * 
     * @param int $idContenido El id del contenido
     * @param int $nImagenes Numero de imágenes que van en la galeria de fotos
     * @return array Array con el contenido desarrollado
     */
    static function getContenidoDesarrollado($idContenido, $nImagenes = 99999) {

        if ($nImagenes <= 0)
            $nImagenes = 99999;

        $arrayComentarios = array();
        
        $contenido = self::getContenido($idContenido);
        $idSeccionVideos = $contenido->getIDSeccionVideos()->getId();
        $videos = ($idSeccionVideos > 0) ? Videos::getVideos($idSeccionVideos, -1) : array();

        if ($contenido->getBlogPublicar()->getIDTipo() == '1') {
            $comentarios = new BlogComentarios();
            $rows = $comentarios->cargaCondicion("Id", "Entidad='GconContenidos' and IdEntidad='{$idContenido}'", "SortOrder DESC");
            unset($comentarios);
            foreach ($rows as $row) {
                $arrayComentarios[] = new BlogComentarios($row['Id']);
            }
        }

        return array(
            'contenido' => $contenido,
            'comentarios' => $arrayComentarios,
            'galeriaFotos' => Albumes::getAlbumExterno('GconContenidos', $idContenido, $nImagenes),
            'enlacesRelacionados' => Enlaces::getEnlacesRelacionados('GconContenidos', $idContenido),
            'docsAdjuntos' => $contenido->getDocuments('document'),
            'videos' => $videos,
        );
    }

    /**
     * Genera el array con las contenidos más visitados
     * 
     * 
     * Las contenidos se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * El array tiene 2 elementos:
     * 
     * - subtitulo => el subtitulo de la noticia (seccion)
     * - url => array(url => texto, targetBlank => boolean)
     * 
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional. (0=todos)
     * @return array Array con los contenidos
     */
    static function getContenidosMasVisitados($nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $contenido = new GconContenidos();

        $rows = $contenido->cargaCondicion("Id", "", "NumberVisits DESC {$limite}");

        foreach ($rows as $row) {
            $contenido = new GconContenidos($row['Id']);
            $array[] = array(
                'titulo' => $contenido->getTitulo(),
                'url' => $contenido->getHref(),
            );
        }
        unset($contenido);

        return $array;
    }

}

?>
