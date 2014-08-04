<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DEL BLOG
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 14-mar-2013
 */
class Blog {

    /**
     * Devuelve un array paginado de secciones de blog
     * 
     * Las secciones van ordenadas según 'SortOrder ASC'
     * 
     * @param int $enPortada Valores posibles: 0, 1 ó 2. Por defecto 1. Indica respectivamente si se devuelven
     * aquellas secciones que tienen artículos de blog que no se muestran en portada, si se muestran o ambos casos.
     * @param int $nPagina El número de página a mostrar. Por defecto la primera.
     * @param int $nItems El número de secciones a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de secciones indicado en las variables
     * web 'NumSeccionesBlogHome' o 'NumSeccionesBlogPorPagina' dependiendo de $enPortada
     * @param int $nImagenDiseno El número de imagen de diseño. Por defecto la primera.
     */
    static function getSecciones($enPortada = 1, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        if ($nPagina <= 0)
            $nPagina = 1;

        if ($nItems <= 0) {
            $variables = CpanVariables::getVariables('Web', 'Mod', 'GconSecciones');
            $nItems = ($enPortada == 1) ?
                    $variables['especificas']['NumSeccionesBlogHome'] :
                    $variables['especificas']['NumSeccionesBlogPorPagina'];
        }

        $filtro = "BlogPublicar='1' AND Publish='1' AND Deleted='0'";
        if (($enPortada == 0) or ($enPortada == 1))
            $filtro .= " AND BlogMostrarEnPortada='{$enPortada}'";

        $criterioOrden = "SortOrder ASC";

        $contenidos = new GconContenidos();
        $db = $contenidos->getDataBaseName();
        $table = $contenidos->getTableName();
        unset($contenidos);

        $condicion = "Id IN (SELECT DISTINCT IdSeccion from {$db}.{$table} WHERE {$filtro})";

        Paginacion::paginar("GconSecciones", $condicion, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row) {
            $seccion = new GconSecciones($row['Id']);

            $arraySecciones[$seccion->getId()] = array(
                'titulo' => $seccion->getTitulo(),
                'subtitulo' => $seccion->getSubtitulo(),
                'introduccion' => $seccion->getIntroduccion(),
                'url' => $seccion->getHref(),
                'nArticulos' => $seccion->getNumberOfContenidos($filtro),
                'imagen' => $seccion->getPathNameImagenN($nImagenDiseno),
                'thumbnail' => $seccion->getPathNameThumbnailN($nImagenDiseno),
            );
        }

        return array(
            'secciones' => $arraySecciones,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array con el artículo del blog correspondiente a $idArticuloBolg
     * 
     * @param int $idArticuloBlog El id de contenido
     * @param int $nImagenDiseno El número de imagen de diseño. Por defecto la primera
     * @return array
     */
    static function getArticulo($idArticuloBlog, $nImagenDiseno = 1) {

        $posts = new BlogComentarios();
        $rows = $posts->cargaCondicion("count(Id) as nPosts", "Entidad='GconContenidos' and IdEntidad='{$idArticuloBlog}'");
        unset($posts);
        $numeroPosts = $rows[0]['nPosts'];

        $articulo = new GconContenidos($idArticuloBlog);
        $fecha = explode(" ", $articulo->getPublishedAt());
        $fecha = explode("-", $fecha[0]);
        $mes = new Meses($fecha[1]);
        $articuloBlog = array(
            'seccion' => $articulo->getIdSeccion()->getTitulo(),
            'fecha' => $articulo->getPublishedAt(),
            'dia' => $fecha[0],
            'mes' => $mes->getDescripcion(),
            'anio' => $fecha[2],
            'titulo' => $articulo->getTitulo(),
            'subtitulo' => $articulo->getSubtitulo(),
            'url' => $articulo->getHref(),
            'resumen' => Textos::limpiaTiny($articulo->getResumen()),
            'desarrollo' => Textos::limpiaTiny($articulo->getDesarrollo()),
            'imagen' => $articulo->getPathNameImagenN($nImagenDiseno),
            'thumbnail' => $articulo->getPathNameThumbnailN($nImagenDiseno),
            'numeroPosts' => $numeroPosts,
        );

        unset($articulo);

        return $articuloBlog;
    }

    /**
     * Genera el array con los articulos del blog en base a los CONTENIDOS que:
     * 
     *      BlogPublicar = 1
     * 
     * Si los articulos a devolver son los de portada, además se tiene en cuenta
     * las variables web del módulo GconContenidos:
     * 
     * - NumArticulosBlogHome, y
     * - NumArticulosBlogPorPagina
     * 
     * El array tiene tres elementos:
     * 
     * - secciones => array:
     * 
     *      - titulo => el título de la sección
     *      - url => array(url => texto, targetBlank => boolean) 
     *      - nArticulos => el número de artículos de la sección
     * 
     * - articulos => array:
     * 
     *      - fecha => la fecha de publicación (PublisehAt) en formato dd-mm-yyyy hh:mm:ss
     *      - dia => el día de publicación en formato dd
     *      - mes => el mes de publicación en formato texto en el idioma en curso
     *      - anio => el año de publicación en formato yyyy
     *      - titulo => titulo de la noticia
     *      - subtitulo => subtitulo de la noticia
     *      - url => array(url => texto, targetBlank => boolean)
     *      - resumen => texto del resumen
     *      - desarrollo => texto del desarrollo
     *      - imagen => Path de la imagen de diseño 1
     *      - thumbnail => Path del thumbnail de la imagen de diseño 1
     * 
     * - paginacion => array de paginación
     * 
     * @param integer $idSeccion Si es mayor que 0, se muestran los artículos de dicha sección.
     * @param boolean $enPortada Si TRUE se devuelven solo los que están marcados como portada, 
     * en caso contrario se devuelven todas los articulos
     * @param integer $nPagina El número de página a mostrar. Por defecto la primera
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de articulos indicado en las variables
     * web 'NumArticulosBlogHome' o 'NumArticulosBlogPorPagina' dependiendo de $enPortada
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getArticulos($idSeccion = 0, $enPortada = true, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayArticulos = array();

        if ($nPagina <= 0)
            $nPagina = 1;
        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0) {
            $nItems = ($enPortada) ?
                    $var['especificas']['NumArticulosBlogHome'] :
                    $var['especificas']['NumArticulosBlogPorPagina'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "BlogPublicar='1'";
        if ($enPortada)
            $filtro .= " AND BlogMostrarEnPortada='{$enPortada}'";

        if ($idSeccion > 0)
            $filtro .= " AND IdSeccion='{$idSeccion}'";

        $criterioOrden = "PublishedAt DESC";

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("Id", $filtro, "{$criterioOrden} {$limite}");

        foreach ($rows as $row) {
            $articulo = new GconContenidos($row['Id']);

            $seccion = $articulo->getIdSeccion();
            if (!isset($arraySecciones[$seccion->getId()])) {
                $arraySecciones[$seccion->getId()] = array(
                    'titulo' => $seccion->getTitulo(),
                    'url' => $seccion->getHref(),
                    'nArticulos' => $seccion->getNumberOfContenidos(),
                );
            }
        }

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayArticulos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'articulos' => $arrayArticulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Genera el array con los articulos del blog publicados en el día $dia, o
     * en su defecto el día en curso
     * 
     * 
     * Si los articulos a devolver son los de portada, además se tiene en cuenta
     * las variables web del módulo GconContenidos:
     * 
     * - NumArticulosBlogHome, y
     * - NumArticulosBlogPorPagina
     * 
     * El array tiene tres elementos:
     * 
     * - secciones => array:
     * 
     *      - titulo => el título de la sección
     *      - url => array(url => texto, targetBlank => boolean) 
     *      - nArticulos => el número de artículos de la sección
     * 
     * - articulos => array:
     * 
     *      - fecha => la fecha de publicación (PublisehAt) en formato dd-mm-yyyy hh:mm:ss
     *      - dia => el día de publicación en formato dd
     *      - mes => el mes de publicación en formato texto en el idioma en curso
     *      - anio => el año de publicación en formato yyyy
     *      - titulo => titulo de la noticia
     *      - subtitulo => subtitulo de la noticia
     *      - url => array(url => texto, targetBlank => boolean)
     *      - resumen => texto del resumen
     *      - desarrollo => texto del desarrollo
     *      - imagen => Path de la imagen de diseño 1
     * 
     * - paginacion => array de paginación
     * 
     * @param date $dia El día para filtrar. Por defecto el actual.
     * @param boolean $enPortada Si TRUE se devuelven solo los que están marcados como portada, 
     * en caso contrario se devuelven todas los articulos
     * @param integer $nPagina El número de página a mostrar. Por defecto la primera
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de articulos indicado en las variables
     * web 'NumArticulosBlogHome' o 'NumArticulosBlogPorPagina' dependiendo de $enPortada
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getArticulosDia($dia = '', $enPortada = true, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayArticulos = array();

        if ($dia == '')
            $dia = date('Y-m-d');
        else {
            // Relleno con ceros el mes y el día
            $fecha = explode("-", $dia);
            $fecha[1] = str_pad($fecha[1], 2, "0", STR_PAD_LEFT);
            $fecha[2] = str_pad($fecha[2], 2, "0", STR_PAD_LEFT);
            $dia = $fecha[0] . "-" . $fecha[1] . "-" . $fecha[2];
        }

        if ($nPagina <= 0)
            $nPagina = 1;
        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0) {
            $nItems = ($enPortada) ?
                    $var['especificas']['NumArticulosBlogHome'] :
                    $var['especificas']['NumArticulosBlogPorPagina'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "BlogPublicar='1' and DATE_FORMAT(PublishedAt,'%Y-%m-%d')='{$dia}'";
        if ($enPortada)
            $filtro .= " AND BlogMostrarEnPortada='{$enPortada}'";

        $criterioOrden = "PublishedAt DESC";

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("Id", $filtro, "{$criterioOrden} {$limite}");

        foreach ($rows as $row) {
            $articulo = new GconContenidos($row['Id']);

            $seccion = $articulo->getIdSeccion();
            if (!isset($arraySecciones[$seccion->getId()])) {
                $arraySecciones[$seccion->getId()] = array(
                    'titulo' => $seccion->getTitulo(),
                    'url' => $seccion->getHref(),
                    'nArticulos' => $seccion->getNumberOfContenidos(),
                );
            }
        }

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayArticulos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'articulos' => $arrayArticulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Genera el array con los articulos del blog en base a los CONTENIDOS que:
     * 
     *      BlogPublicar = 1
     * 
     * Si los articulos a devolver son los de portada, además se tiene en cuenta
     * las variables web del módulo GconContenidos:
     * 
     * - NumArticulosBlogHome, y
     * - NumArticulosBlogPorPagina
     * 
     * El array tiene tres elementos:
     * 
     * - secciones => array:
     * 
     *      - titulo => el título de la sección
     *      - url => array(url => texto, targetBlank => boolean) 
     *      - nArticulos => el número de artículos de la sección
     * 
     * - articulos => array:
     * 
     *      - fecha => la fecha de publicación (PublisehAt) en formato dd-mm-yyyy hh:mm:ss
     *      - dia => el día de publicación en formato dd
     *      - mes => el mes de publicación en formato texto en el idioma en curso
     *      - anio => el año de publicación en formato yyyy
     *      - titulo => titulo de la noticia
     *      - subtitulo => subtitulo de la noticia
     *      - url => array(url => texto, targetBlank => boolean)
     *      - resumen => texto del resumen
     *      - desarrollo => texto del desarrollo
     *      - imagen => Path de la imagen de diseño 1
     * 
     * - paginacion => array de paginación
     * 
     * @param boolean $enPortada Si TRUE se devuelven solo los que están marcados como portada, 
     * en caso contrario se devuelven todas los articulos
     * @param integer $nPagina El número de página a mostrar. Por defecto la primera
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de articulos indicado en las variables
     * web 'NumArticulosBlogHome' o 'NumArticulosBlogPorPagina' dependiendo de $enPortada
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getArticulosMes($anio, $mes, $enPortada = true, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayArticulos = array();

        if ($anio <= 0)
            $anio = date('Y');
        if ($mes < 1 or $mes > 12)
            $mes = date('m');

        if ($nPagina <= 0)
            $nPagina = 1;
        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0) {
            $nItems = ($enPortada) ?
                    $var['especificas']['NumArticulosBlogHome'] :
                    $var['especificas']['NumArticulosBlogPorPagina'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "BlogPublicar='1' and YEAR(PublishedAt)='{$anio}' and MONTH(PublishedAt)='{$mes}'";
        if ($enPortada)
            $filtro .= " AND BlogMostrarEnPortada='{$enPortada}'";

        $criterioOrden = "PublishedAt DESC";

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("Id", $filtro, "{$criterioOrden} {$limite}");

        foreach ($rows as $row) {
            $articulo = new GconContenidos($row['Id']);

            $seccion = $articulo->getIdSeccion();
            if (!isset($arraySecciones[$seccion->getId()])) {
                $arraySecciones[$seccion->getId()] = array(
                    'titulo' => $seccion->getTitulo(),
                    'url' => $seccion->getHref(),
                    'nArticulos' => $seccion->getNumberOfContenidos(),
                );
            }
        }

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayArticulos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($mes);
        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'articulos' => $arrayArticulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Genera el array con los articulos del blog en base a los CONTENIDOS que:
     * 
     *      BlogPublicar = 1
     * 
     * Si los articulos a devolver son los de portada, además se tiene en cuenta
     * las variables web del módulo GconContenidos:
     * 
     * - NumArticulosBlogHome, y
     * - NumArticulosBlogPorPagina
     * 
     * El array tiene tres elementos:
     * 
     * - secciones => array:
     * 
     *      - titulo => el título de la sección
     *      - url => array(url => texto, targetBlank => boolean) 
     *      - nArticulos => el número de artículos de la sección
     * 
     * - articulos => array:
     * 
     *      - fecha => la fecha de publicación (PublisehAt) en formato dd-mm-yyyy hh:mm:ss
     *      - dia => el día de publicación en formato dd
     *      - mes => el mes de publicación en formato texto en el idioma en curso
     *      - anio => el año de publicación en formato yyyy
     *      - titulo => titulo de la noticia
     *      - subtitulo => subtitulo de la noticia
     *      - url => array(url => texto, targetBlank => boolean)
     *      - resumen => texto del resumen
     *      - desarrollo => texto del desarrollo
     *      - imagen => Path de la imagen de diseño 1
     * 
     * - paginacion => array de paginación
     * 
     * @param boolean $enPortada Si TRUE se devuelven solo los que están marcados como portada, 
     * en caso contrario se devuelven todas los articulos
     * @param integer $nPagina El número de página a mostrar. Por defecto la primera
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de articulos indicado en las variables
     * web 'NumArticulosBlogHome' o 'NumArticulosBlogPorPagina' dependiendo de $enPortada
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getArticulosBusqueda($texto, $enPortada = true, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayArticulos = array();

        if ($nPagina <= 0)
            $nPagina = 1;
        $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
        if ($nItems <= 0) {
            $nItems = ($enPortada) ?
                    $var['especificas']['NumArticulosBlogHome'] :
                    $var['especificas']['NumArticulosBlogPorPagina'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "BlogPublicar='1' and (Titulo like '%{$texto}%' or Subtitulo like '%{$texto}%' or Resumen like '%{$texto}%')";
        if ($enPortada)
            $filtro .= " AND BlogMostrarEnPortada='{$enPortada}'";

        $criterioOrden = "PublishedAt DESC";

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("Id", $filtro, "{$criterioOrden} {$limite}");

        foreach ($rows as $row) {
            $articulo = new GconContenidos($row['Id']);

            $seccion = $articulo->getIdSeccion();
            if (!isset($arraySecciones[$seccion->getId()])) {
                $arraySecciones[$seccion->getId()] = array(
                    'titulo' => $seccion->getTitulo(),
                    'url' => $seccion->getHref(),
                    'nArticulos' => $seccion->getNumberOfContenidos(),
                );
            }
        }

        Paginacion::paginar("GconContenidos", $filtro, $criterioOrden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $arrayArticulos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'articulos' => $arrayArticulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Genera el array con los artículos del blog mas visitados
     * 
     * Las artículos se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * Si no se indica el parámetro $nItems, se buscará el valor de la variable
     * web 'NumArticulosBlogHome'
     * 
     * El array consta de los siguientes elementos:
     * 
     * - fecha => la fecha de publicación (PublishedAt)
     * - titulo => el titulo del artículo
     * - subtitulo => el subtitulo del artículo
     * - url => array(url => texto, targetBlank => boolean)
     * - resumen => el resumen del artículo
     * - desarrollo => el desarrollo dl artículo
     * - imagen => Path de la imagen de diseño $nImagenDiseno
     * - thumbnail => Path del thumbnail del la imagen de diseño $nImagenDiseno
     * 
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrarán las indicadas en la VW 'NumArticulosBlogHome'
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con los artículos
     */
    static function getArticulosMasVisitados($nItems = 0, $nImagenDiseno = 1) {

        $array = array();

        if ($nItems <= 0) {
            $this->setVariables('Web', 'Mod', 'GconContenidos');
            $nItems = $_SESSION['varWeb']['Mod']['GconContenidos']['especificas']['NumArticulosBlogHome'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $blog = new GconContenidos();
        $filtro = "BlogPublicar='1'";

        $rows = $blog->cargaCondicion("Id", $filtro, "NumberVisits DESC {$limite}");

        foreach ($rows as $row) {
            $blog = new GconContenidos($row['Id']);
            $array[] = array(
                'fecha' => $blog->getPublishedAt(),
                'titulo' => $blog->getTitulo(),
                'subtitulo' => $blog->getSubtitulo(),
                'url' => $blog->getHref(),
                'resumen' => Textos::limpiaTiny($blog->getResumen()),
                'desarrollo' => Textos::limpiaTiny($blog->getDesarrollo()),
                'imagen' => $blog->getPathNameImagenN($nImagenDiseno),
                'thumbnail' => $blog->getPathNameThumbnailN($nImagenDiseno),
            );
        }
        unset($blog);

        return $array;
    }

    /**
     * Devuelve un array resumen con el número de artículos por meses y año
     * 
     * El array tendrá tantos elementos como se indiquen en $nMeses y estarán
     * ordenados descendentemente por año y mes.
     * 
     * El array es:
     * 
     * - anio => El año numérico
     * - numeroMes => El mes numérico
     * - textoMes => El literal del mes en el idioma en curso
     * - nArticulos => El número de artículos para el ano y mes
     * 
     * @param int $nMeses Número de meses. Por defecto 6
     * @return array Array
     */
    static function getArticulosMeses($nMeses = 6) {

        $array = array();

        if ($nMeses <= 0)
            $nMeses = 6;

        $limite = "LIMIT {$nMeses}";

        $filtro = "BlogPublicar='1'";

        $criterioOrden = "anio DESC, mes DESC";

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("YEAR(PublishedAt) as anio, MONTH(PublishedAt) as mes, COUNT(Id) as nArticulos", "{$filtro} GROUP BY anio, mes", "{$criterioOrden} {$limite}");
        unset($articulo);

        foreach ($rows as $row) {
            $mes = new Meses($row['mes']);
            $array[] = array(
                'anio' => $row['anio'],
                'numeroMes' => $row['mes'],
                'textoMes' => $mes->getDescripcion(),
                'nArticulos' => $row['nArticulos'],
            );
        }

        unset($mes);

        return $array;
    }

    /**
     * Devuelve un array con los dias del mes en los que hay artículos del blog
     * 
     * El índice del array es el ordinal del día del mes y el valor es
     * el número de artículos del blog de ese día.
     * 
     * @param integer $mes El mes
     * @param integer $ano El año
     * @return array Array de pares dia=>nArticulos
     */
    static function getDiasConArticulos($mes, $ano) {

        $array = array();

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("DAY(PublishedAt) dia, COUNT(Id) nArticulos", "BlogPublicar='1' AND MONTH(PublishedAt)='{$mes}' AND YEAR(PublishedAt)='{$ano}' GROUP BY dia");
        unset($articulo);

        foreach ($rows as $row)
            $array[$row['dia']] = $row['nArticulos'];

        return $array;
    }

    /**
     * Devuelve array de objetos comentarios de la entidad $entidad y $idEntidad
     * 
     * 
     * @param string $entidad El nombre de la entidad
     * @param integer $idEntidad El id de la entidad
     * @param integer $nItems Opcional. Por defecto todos
     * @param string $orden Opcional. Por defecto descendente por fecha
     * @param boolean $checked Opcional. Por defecto solo los aprobados
     * @return \BlogComentarios Array de objetos comentarios
     */
    static function getComentarios($entidad, $idEntidad, $nItems = 0, $orden = "TiempoUnix DESC", $checked = true) {

        $chequeado = ($checked) ? 1 : 0;
        $filtro = "Entidad='{$entidad}' AND IdEntidad='{$idEntidad}' AND Checked='{$chequeado}'";
        $limite = ($nItems > 0) ? " LIMIT {$nItems}" : "";

        $comentario = new BlogComentarios();
        $rows = $comentario->cargaCondicion("Id", $filtro, $orden . $limite);
        unset($comentario);

        $array = array();
        foreach ($rows as $row) {
            $array[] = new BlogComentarios($row['Id']);
        }

        return $array;
    }

}

?>
