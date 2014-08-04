<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE LOS EVENTOS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Eventos {

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
    static function getEventos($desdeFecha = '', $hastaFecha = '', $nItems = 0, $nImagenDiseno = 1, $unicos = 1) {

        $array = array();

        if ($desdeFecha == "") {
            $desdeFecha = date('Y-m-d');
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $evento = new EvenEventos();
        $filtro = "Fecha>='{$desdeFecha}'";
        if ($hastaFecha != "") {
            $filtro .= " AND Fecha<='{$hastaFecha}'";
        }

        $rows = $evento->cargaCondicion("Entidad,IdEntidad,Fecha,HoraInicio,HoraFin", $filtro, "Fecha ASC, HoraInicio ASC {$limite}");
        unset($evento);
        $eventos = array();
        if ($unicos) {
            foreach ($rows as $row) {
                if (!isset($eventos[$row['Entidad'] . $row['IdEntidad']])) {
                    $eventos[$row['Entidad'] . $row['IdEntidad']] = $row;
                }
            }
        } else {
            $eventos = $rows;
        }

        foreach ($eventos as $row) {
            $evento = new $row['Entidad']($row['IdEntidad']);
            if ($evento->getPublish()->getIdTipo() == '1') {
                $array[] = array(
                    'fecha' => $row['Fecha'],
                    'horaInicio' => $row['HoraInicio'],
                    'horaFin' => $row['HoraFin'],
                    'titulo' => $evento->getTitulo(),
                    'subtitulo' => $evento->getSubtitulo(),
                    'url' => $evento->getHref(),
                    'resumen' => Textos::limpiaTiny($evento->getResumen()),
                    'desarrollo' => Textos::limpiaTiny($evento->getDesarrollo()),
                    'imagen' => $evento->getPathNameImagenN($nImagenDiseno),
                );
            }
        }
        unset($evento);

        return $array;
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

        $array = array();

        $evento = new EvenEventos();
        $rows = $evento->cargaCondicion("DAY(Fecha) dia, COUNT(Id) nEventos", "MONTH(Fecha)='{$mes}' AND YEAR(Fecha)='{$ano}' GROUP BY dia");
        unset($evento);

        foreach ($rows as $row) {
            $array[$row['dia']] = $row['nEventos'];
        }

        return $array;
    }

}

?>
