<?php

/**
 * CONTENEDOR DE WIDGETS
 * 
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 27-oct-2013
 */
class Widgets {

    /**
     * Devuelve el calendario
     * @param integer $mes
     * @param integer $anio
     * @param string $tipo
     * @return array
     */
    public function getCalendario($mes = '', $anio = '', $tipo = 'Eventos') {
        return Calendario::getCalendario($mes, $anio, $tipo);
    }

    /**
     * Devuelve las categorias de productos
     * @param boolean $enPortada
     * @param integer $nitems
     * @return array
     */
    public function getCategorias($enPortada = true, $nitems = 0) {
        return ErpFamilias::getCategoriasFamilias($enPortada, $nitems);
    }

    /**
     * Devuelve las marcas/fabricantes
     * @param boolean $enPortada
     * @param integer $posicionInicio
     * @param integer $nItems. por defecto todas
     * @return array
     */
    public function getMarcas($enPortada = true, $posicionInicio = 0, $nItems = 0) {
        return ErpMarcas::getMarcas($enPortada, $posicionInicio, $nItems);
    }

    /**
     * Devuelve un array con las categorias y familias de los
     * artículos que son del fabricante del $idMarca
     * 
     * @param integer $idMarca EL id del fabricante/marca
     * @return array
     */
    public function getCategoriasFamiliaMarca($idMarca) {
        $marca = new Fabricantes($idMarca);
        $array = $marca->getCategoriasFamilias();
        unset($marca);
        return $array;
    }

    /**
     * Devuelve los artículos asociados a un controlador y una zona
     * según lo definido en el esquelo web
     * 
     * @param string $controller
     * @param integer $zona
     * @return array
     */
    public function getArticulosZona($controller, $zona, $filtroAdicional = '1') {
        return ErpArticulos::getArticulosZona($controller, $zona, $filtroAdicional);
    }

    /**
     * Devuelve los artículos más visitados
     * 
     * @param integer $nItems El número de artículos a devolver. Por defecto 10
     * @return array de objetos artículos
     */
    public function getArticulosMasVistos($nItems = 10) {
        return ErpArticulos::getLosMasVistos($nItems);
    }

    /**
     * Devuelve un array con BANNERS.
     * 
     * @param int $zona El nombre de la zona de banner a filtrar. Opcional. Defecto la primera que encuentre.
     * @param int $tipo El tipo de banner. Un valor negativo significa todos los tipos. Por defecto 0 (fijo). Valores posibles en entities/abstract/TiposBanners.class.php
     * @param boolean $mostrarEnListado Un valor negativo para todos, 0 para los NO y 1 para los SI mostrar en listado
     * @param int $nItems Número máximo de banners a devolver. Opcional. Defecto todos
     * @return array Array de banners
     */
    public function getBanners($zona = '*', $tipo = 0, $mostrarEnListado = 0, $nItems = 0) {
        return Banners::getBanners($zona, $tipo, $mostrarEnListado, $nItems);
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
    public function getNoticias($enPortada = true, $nItems = 0, $nPagina = 1, $nImagenDiseno = 1) {
        return Noticias::getNoticias($enPortada, $nItems, $nPagina, $nImagenDiseno);
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
    public function getNoticasMasLeidas($nItems = 0, $nImagenDiseno = 1) {
        return Noticias::getNoticiasMasLeidas($nItems, $nImagenDiseno);
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
    public function getDiasConNoticias($mes, $ano) {
        return Noticias::getDiasConNoticias($mes, $ano);
    }

    /**
     * Devuelve un array con contenidos que son EVENTOS.
     * 
     * Los contenidos que se devuelven deben estar marcados con EVENTO,
     * tener asignados fechas de evento y estar marcados como publicados.
     * 
     * Están ordenados ASCENDENTEMENTE por Fecha y Hora de inicio
     * 
     * El array tiene los siguientes elementos:
     * 
     * - fecha => la fecha del evento
     * - horaInicio => la hora de inicio del evento
     * - horaFin => La hora de finalización del evento
     * - titulo => el titulo del evento
     * - subtitulo => el subtitulo del evento
     * - url => array(url => texto, targetBlank => boolean)
     * - resumen => el texto resumen del evento
     * - desarrollo => el texto desarrollado del evento
     * - imagen => Path de la imagen de diseño 1
     * 
     * @param date $desdeFecha La fecha en formato aaaa-mm-dd a partir desde la que se muestran los eventos. Opcional. Defecto hoy
     * @param date $hastaFecha La fecha en formato aaaa-mm-dd hasta cuando se muestran los eventos. Opcional. Defecto sin límite
     * @param integer $nItems El numero máximo de elementos a devolver. (0=todos)
     * @param integer $nImagenDiseno El número de la imagen de diseño. Por defecto la primera
     * @param boolean $unicos Si TRUE solo se devuelven los eventos únicos
     * @return array Array con los eventos
     */
    public function getEventos($desdeFecha = '', $hastaFecha = '', $nItems = 0, $nImagenDiseno = 1, $unicos = 1) {
        return Eventos::getEventos($desdeFecha, $hastaFecha, $nItems, $nImagenDiseno, $unicos);
    }

    /**
     * Devuelve un array con los dias del mes en los que hay eventos
     * 
     * El índice del array es el ordinal del día del mes y el valor es
     * el número de eventos de ese día.
     * 
     * @param integer $mes El mes
     * @param integer $ano El año
     * @return array Array de pares dia=>nEventos
     */
    static function getDiasConEventos($mes, $ano) {
        return Eventos::getDiasConEventos($mes, $ano);
    }

    /**
     * Devuelve array con la información del tiempo
     * stdClass Object
(
    [coord] => stdClass Object
        (
            [lon] => -3.63
            [lat] => 37.21
        )

    [sys] => stdClass Object
        (
            [message] => 0.0092
            [country] => ES
            [sunrise] => 1395555162
            [sunset] => 1395599352
        )

    [weather] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 800
                    [main] => Clear
                    [description] => cielo claro
                    [icon] => 01d
                )

        )

    [base] => cmc stations
    [main] => stdClass Object
        (
            [temp] => 16.11
            [humidity] => 23
            [pressure] => 1014
            [temp_min] => 16.11
            [temp_max] => 16.11
        )

    [wind] => stdClass Object
        (
            [speed] => 3.6
            [deg] => 289
        )

    [clouds] => stdClass Object
        (
            [all] => 0
        )

    [dt] => 1395593091
    [id] => 2512767
    [name] => Peligros
    [cod] => 200
)
     * @return array
     */
    static function getWeather() {
        $var = CpanVariables::getVariables('Web', 'Pro');
        return WebService::getWeather($var['meta']['geoPositionLatitude'], $var['meta']['geoPositionLongitude']);
    }
}

?>
