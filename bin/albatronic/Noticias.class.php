<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE LAS NOTICIAS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Noticias {

    static function getNoticia($idNoticia, $nImagenDiseno) {
        return new GconContenidos($idNoticia);
        /**
          $noticia = new GconContenidos($idNoticia);
          $array = array(
          'fecha' => $noticia->getPublishedAt(),
          'titulo' => $noticia->getTitulo(),
          'subtitulo' => $noticia->getSubtitulo(),
          'url' => $noticia->getHref(),
          'resumen' => Textos::limpiaTiny($noticia->getResumen()),
          'desarrollo' => Textos::limpiaTiny($noticia->getDesarrollo()),
          'imagen' => $noticia->getPathNameImagensN($nImagenDiseno),
          'thumbnail' => $noticia->getPathNameThumbnailN($nImagenDiseno),
          );
          unset($noticia);

          return $array;

         */
    }

    /**
     * Genera el array con las noticias en base a los CONTENIDOS que:
     * 
     *      NoticiaPublicar = 1
     * 
     * Si las noticias a devolver son las de portada, además se tiene en cuenta
     * las variables web del módulo GconContenidos:
     * 
     * - NumNoticasMostrarHome, y
     * - NumNoticasPorPagina
     * 
     * El array tiene los siguientes elementos
     * 
     * - rows. Con n elementos de la forma:
     *   - fecha => la fecha de publicación (PublisehAt)
     *   - titulo => titulo de la noticia
     *   - subtitulo => subtitulo de la noticia
     *   - url => array(url => texto, targetBlank => boolean)
     *   - resumen => texto del resumen
     *   - desarrollo => texto del desarrollo
     *   - imagen => Path de la imagen de diseño 1
     * - pagina => El número de la página actual
     * - numeroPaginas => El número total de páginas
     * - numeroTotalItems => El número total de noticias
     * 
     * @param boolean $enPortada Si TRUE se devuelven solo las que están marcadas como portada, 
     * en caso contrario se devuelven todas las noticias
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de noticias indicado en las variables
     * web 'NumNoticasMostrarHome' o 'NumNoticasPorPagina' dependiendo de $enPortada
     * @param int $nPagina El número de la página. Opcional. Por defecto la primera
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getNoticias($enPortada = true, $nItems = 0, $nPagina = 1, $nImagenDiseno = 1) {

        $variables = CpanVariables::getVariables('Web', 'Mod', 'GconContenidos');

        if ($nItems <= 0) {
            $nItems = ($enPortada) ?
                    $variables['especificas']['NumNoticiasHome'] :
                    $variables['especificas']['NumNoticiasPorPagina'];
        }

        if ($nPagina <= 0) {
            $nPagina = 1;
        }

        $filtro = "NoticiaPublicar='1'";
        if ($enPortada) {
            $filtro .= " AND NoticiaMostrarEnPortada='{$enPortada}'";
        }
        $criterioOrden = $variables['especificas']['CriterioOrdenNoticias'];

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row) {
            $paginacion['rows'][] = self::getNoticia($row['Id'], $nImagenDiseno);
        }

        $paginacion['paginacion'] = Paginacion::getPaginacion();

        return $paginacion;
    }

    /**
     * Genera el array con las noticias más leidas
     * 
     * Las noticias son Contenidos que tienen a TRUE el campo NoticiaPublicar
     * 
     * Las noticias se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * Si no se indica el parámetro $nItems, se buscará el valor de la variable
     * web 'NumNoticasMostrarHome'
     * 
     * El array los siguientes elementos:
     * 
     * - fecha => la fecha de publicación (PublishedAt)
     * - titulo => el titulo de la noticia (seccion)
     * - subtitulo => el subtitulo de la noticia (seccion)
     * - url => array(url => texto, targetBlank => boolean)
     * - resumen => el resumen de la noticia (seccion)
     * - desarrollo => el desarrollo de la noticia
     * - imagen => Path de la imagen de diseño $nImagenDiseno
     * - thumbnail => Path del thumbnail del la imagen de diseño $nImagenDiseno
     * 
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrarán las indicadas en la VW 'NumNoticasMostrarHome'
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getNoticiasMasLeidas($nItems = 0, $nImagenDiseno = 1) {

        $array = array();

        if ($nItems <= 0) {
            $variables = CpanVariables::getVariables('Web', 'Mod', 'GconContenidos');
            $nItems = $variables['especificas']['NumNoticiasHome'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $noticia = new GconContenidos();
        $rows = $noticia->cargaCondicion("Id", "NoticiaPublicar='1'", "NumberVisits DESC {$limite}");
        unset($noticia);

        foreach ($rows as $row) {
            $array[] = self::getNoticia($row['Id'], $nImagenDiseno);
        }

        return $array;
    }

    /**
     * Devuelve un array con los dias del mes en los que hay noticias
     * 
     * El índice del array es el ordinal del día del mes y el valor es
     * el número de noticias de ese día.
     * 
     * @param integer $mes El mes
     * @param integer $ano El año
     * @return array Array de pares dia=>nNoticias
     */
    static function getDiasConNoticias($mes, $ano) {

        $array = array();

        $noticia = new GconContenidos();
        $rows = $noticia->cargaCondicion("DAY(PublishedAt) dia, COUNT(Id) nNoticias", "NoticiaPublicar='1' AND MONTH(PublishedAt)='{$mes}' AND YEAR(PublishedAt)='{$ano}' GROUP BY dia");
        unset($noticia);

        foreach ($rows as $row) {
            $array[$row['dia']] = $row['nNoticias'];
        }

        return $array;
    }

}

?>
