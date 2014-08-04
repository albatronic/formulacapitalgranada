<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE LA WIKI
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 21-may-2013
 */
class Wiki {

    /**
     * Devuelve un array con el artículo de la wiki correspondiente a $idArticuloWiki
     * 
     * @param int $idArticuloWiki El id de contenido
     * @param int $nImagenDiseno El número de imagen de diseño. Por defecto la primera
     * @return array
     */
    static function getArticulo($idArticuloWiki, $nImagenDiseno = 1) {

        $articulo = new GconContenidos($idArticuloWiki);
        $fecha = explode(" ", $articulo->getPublishedAt());
        $fecha = explode("-", $fecha[0]);
        $mes = new Meses($fecha[1]);
        $articuloWiki = array(
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
        );

        unset($articulo);

        return $articuloWiki;
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
     * Genera el array con los articulos de la wiki en base a los CONTENIDOS que:
     * 
     *      EsWiki = 1
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
     * @param integer $nPagina El número de página a mostrar. Por defecto la primera
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional.
     * Si no se indica valor, se mostrará el número de articulos indicado en las variables
     * web 'NumArticulosBlogHome' o 'NumArticulosBlogPorPagina' dependiendo de $enPortada
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con las noticias
     */
    static function getArticulosBusqueda($texto, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayTerminos = array();

        if ($nPagina <= 0)
            $nPagina = 1;
        
        if ($nItems <= 0) {
            $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
            $nItems = $var['especificas']['NumArticulosWikiPorPagina'];
        }

        $filtro = "EsWiki='1' and (Titulo like '%{$texto}%' or Subtitulo like '%{$texto}%' or Resumen like '%{$texto}%')";
        $criterioOrden = "Titulo ASC";
        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";        

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
            $arrayTerminos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'terminos' => $arrayTerminos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un listado paginado de los términos de la wiki
     * cuyo título empieza por la letra $letra, ordenado por
     * título ascendentemente.
     * 
     * @param string $letra La letra por la que empieza el titulo
     * @param int $nPagina El número de página. Por defecto la primera
     * @param int $nItems El número de términos a mostrar. Por defecto la variable web 'NumArticulosWikiPorPagina'
     * @param int $nImagenDiseno El número de imagen de diseño. Por defecto la primera
     * @return array
     */
    static function getArticulosAlfabeto($letra, $nPagina = 1, $nItems = 0, $nImagenDiseno = 1) {

        $arraySecciones = array();
        $arrayTerminos = array();

        if ($nPagina <= 0)
            $nPagina = 1;

        if ($nItems <= 0) {
            $var = CpanVariables::getVariables("Web", "Mod", "GconContenidos");
            $nItems = $var['especificas']['NumArticulosWikiPorPagina'];
        }

        $filtro = "EsWiki='1' and (Titulo like '{$letra}%')";
        $criterioOrden = "Titulo ASC";
        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";        

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
            $arrayTerminos[] = self::getArticulo($row['Id'], $nImagenDiseno);

        unset($seccion);
        unset($articulo);

        asort($arraySecciones);

        return array(
            'secciones' => $arraySecciones,
            'terminos' => $arrayTerminos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Genera el array con los artículos de la wiki mas visitados
     * 
     * Las artículos se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * Si no se indica el parámetro $nItems, se buscará el valor de la variable
     * web 'NumArticulosWikiHome'
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
     * Si no se indica valor, se mostrarán las indicadas en la VW 'NumArticulosWikiHome'
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con los artículos
     */
    static function getArticulosMasVisitados($nItems = 0, $nImagenDiseno = 1) {

        $array = array();

        if ($nItems <= 0) {
            $var = CpanVariables::getVariables('Web', 'Mod', 'GconContenidos');
            $nItems = $var['especificas']['NumArticulosWikiHome'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $wiki = new GconContenidos();
        $rows = $wiki->cargaCondicion("Id", "EsWiki='1'", "NumberVisits DESC {$limite}");
        unset($wiki);

        foreach ($rows as $row)
            $array[] = self::getArticulo($row['Id'], $nImagenDiseno);

        return $array;
    }

    /**
     * Genera el array con los últimos términos introducidos en la wiki
     * 
     * Las artículos se ordenan descendentemente según la fecha de publicación
     * 
     * Si no se indica el parámetro $nItems, se buscará el valor de la variable
     * web 'NumArticulosWikiHome'
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
     * Si no se indica valor, se mostrarán las indicadas en la VW 'NumArticulosWikiHome'
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @return array Array con los artículos
     */
    static function getUltimosTerminos($nItems = 0, $nImagenDiseno = 1) {

        $array = array();

        if ($nItems <= 0) {
            $var = CpanVariables::getVariables('Web', 'Mod', 'GconContenidos');
            $nItems = $var['especificas']['NumArticulosWikiHome'];
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $wiki = new GconContenidos();
        $rows = $wiki->cargaCondicion("Id", "EsWiki='1'", "PublishedAt DESC {$limite}");
        unset($wiki);

        foreach ($rows as $row)
            $array[] = self::getArticulo($row['Id'], $nImagenDiseno);

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

        $filtro = "EsWiki='1'";

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
     * Devuelve un array con los dias del mes en los que hay artículos de la wiki
     * 
     * El índice del array es el ordinal del día del mes y el valor es
     * el número de artículos de la wiki de ese día.
     * 
     * @param integer $mes El mes
     * @param integer $ano El año
     * @return array Array de pares dia=>nArticulos
     */
    static function getDiasConArticulos($mes, $ano) {

        $array = array();

        $articulo = new GconContenidos();
        $rows = $articulo->cargaCondicion("DAY(PublishedAt) dia, COUNT(Id) nArticulos", "EsWiki='1' AND MONTH(PublishedAt)='{$mes}' AND YEAR(PublishedAt)='{$ano}' GROUP BY dia");
        unset($articulo);

        foreach ($rows as $row)
            $array[$row['dia']] = $row['nArticulos'];

        return $array;
    }

}

?>
