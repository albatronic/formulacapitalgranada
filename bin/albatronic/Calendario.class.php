<?php

/**
 * CLASE ESTATICA PARA LA GESTION DEL CALENDARIO
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática Albatronic, SL
 * @version 1.0 27-nov-2012
 */
class Calendario {

    static $mes;
    static $ano;

    /**
     * Correspondencia entre el tipo de evento
     * a mostrar en el día del calendario y el controller a lanzar
     * @var array
     */
    static $evento = array(
        "Eventos" => "eventos",
        "Blog" => "blog",
        "Noticias" => "contenidos",
    );

    /**
     * Genera el array 'calendario' del mes y año en curso
     * incluyendo las marcas en los días que haya eventos
     * 
     * Los elmentos del array son:
     * 
     * - calendario => texto html con el calendario
     * - mesTexto => El nombre del mes
     * - mesNumero => El número del mes
     * - ano => el número del año con cuatro cifras
     * 
     * @param integer $mes EL número de mes. Opcional. Por defecto el mes en curso
     * @param integer $ano El año. Opcional. Por defecto el año en curso
     * @param string $tipo El tipo de eventos a mostrar (eventos,noticias,blog). Por defecto 'Eventos'
     * @return array Array con los elementos del calendio
     */
    static function getCalendario($mes = '', $ano = '', $tipo = 'Eventos') {
        return array(
            'calendario' => self::showCalendario($mes, $ano, $tipo),
            'mesTexto' => self::getMes(1),
            'mesNumero' => self::getMes(0),
            'ano' => self::getAno(),
            'tipo' => $tipo,
        );
    }

    /**
     * Devuelve el código html del calendario del $mes y $ano
     * 
     * @param integer $mes El número de mes a mostrar. Por defecto el actual
     * @param integer $ano El año a mostrar. Por defecto el actual
     * @param string $tipo El tipo del evento a mostrar (eventos,noticias,blog). Por defecto 'Eventos'
     * @param boolean $pintarSemana Generar el número de la semana dentro del año. Por defecto FALSE
     * @param integer $nCaracteresDiaSemana El número de caracteres a mostrar para los títulos de los días de la semana
     * @return string Codigo html 
     */
    static function showCalendario($mes = '', $ano = '', $tipo = 'Eventos', $pintarSemana = false, $nCaracteresDiaSemana = 3) {
        return self::getHtmlCalendario(
                        self::getArrayCalendario($mes, $ano, $tipo), self::$evento[$tipo], $pintarSemana, $nCaracteresDiaSemana
        );
    }

    /**
     * Devuelve el mes del calendario actual
     * 
     * @param integer $formato El tipo de formato a devolver: 0 = numérico, 1 = texto
     * @return string El mes en formato numérico o texto segun $formato
     */
    static function getMes($formato = '0') {
        switch ($formato) {
            case '0':
                $tituloMes = self::$mes;
                break;
            case '1':
            default:
                $meses = new Meses(self::$mes);
                $tituloMes = $meses->getDescripcion();
                unset($meses);
                return $tituloMes;
        }

        return $tituloMes;
    }

    /**
     * Devuelve el año del calendario actual
     * 
     * @return integer
     */
    static function getAno() {
        return self::$ano;
    }

    /**
     * Devuelve el array bidimensional del calendario del $mes y $ano
     * 
     * Cada día es un array con dos elementos: 'dia' y 'nEventos'; donde dia tiene
     * el número ordinal de día dentro del mes y 'nEventos' tiene el número de eventos de ese día
     * 
     * @param integer $mes El mes
     * @param integer $ano El año
     * @param string $tipo El tipo del evento a mostrar (eventos,noticias,blog). Por defecto 'Eventos'
     * @param string $formato El formato cómo se devuelve: php,json,yml
     * @return array Array con el calendario
     */
    static function getArrayCalendario($mes = '', $ano = '', $tipo = '', $formato = 'PHP') {

        self::$mes = ($mes == '') ? date('m') : $mes;
        self::$ano = ($ano == '') ? date('Y') : $ano;

        $calendario = array();
        $calendario['ano'] = self::$ano;
        $calendario['mes'] = self::$mes;

        $formato = strtoupper($formato);

        if (!in_array($formato, array('PHP', 'YML', 'JSON')))
            $formato = 'PHP';

        $ultimoDiaMes = date('d', mktime(0, 0, 0, self::$mes + 1, 0, self::$ano));

        switch ($tipo) {
            case 'Eventos':
                $eventos = Eventos::getDiasConEventos(self::$mes, self::$ano);
                break;
            case 'Noticias':
                $eventos = Noticias::getDiasConNoticias(self::$mes, self::$ano);
                break;
            case 'Blog':
                $eventos = Blog::getDiasConArticulos(self::$mes, self::$ano);
                break;
            default :
                $eventos = array();
        }

        for ($dia = 1; $dia <= $ultimoDiaMes; $dia++) {
            $semana = date('W', mktime(0, 0, 0, self::$mes, $dia, self::$ano));
            $diaSemana = date('N', mktime(0, 0, 0, self::$mes, $dia, self::$ano));
            $calendario['semanas'][$semana][$diaSemana] = array(
                'dia' => $dia,
                'nEventos' => (isset($eventos[$dia])) ? $eventos[$dia] : 0,
            );
        }
        unset($eventos);

        switch ($formato) {
            case 'PHP': $resultado = $calendario;
                break;
            case 'YML': $resultado = sfYaml::dump($calendario);
                break;
            case 'JSON': $resultado = json_encode($calendario);
                break;
        }

        return ($resultado);
    }

    /**
     * Genera el código html de un calendario mensual
     * 
     * @param array $calendario Array bidemensional con el calendario
     * @param string $tipo El tipo de evento a mostar
     * @param boolean $pintarSemana Si true en añade una columna a la izquierda con el número de la semana
     * @param integer $nCaracteresDiaSemana El número de caracteres a mostrar para los títulos de los días de la semana
     * @return string Código html con el calendario
     */
    static function getHtmlCalendario($calendario, $tipo, $pintarSemana = false, $nCaracteresDiaSemana = 3) {

        $cabecera = "<tr>";
        if ($pintarSemana)
            $cabecera .= "<td class='nombredias_calendario'>Sem</td>";

        $diasSemana = new DiasSemana();
        foreach ($diasSemana->fetchAll('', false) as $dia) {
            $dia = substr($dia['Value'], 0, $nCaracteresDiaSemana);
            $cabecera .= "<td class='nombredias_calendario'>{$dia}</td>";
        }
        $cabecera .= "</tr>";
        unset($diasSemana);

        $cuerpo = "";
        foreach ($calendario['semanas'] as $keySemana => $semana) {

            $cuerpo .= "<tr>";
            if ($pintarSemana)
                $cuerpo .= "<td>{$keySemana}</td>";
            for ($dia = 1; $dia <= 7; $dia++) {
                if (isset($semana[$dia])) {
                    if ($semana[$dia]['nEventos'])
                        $cuerpo .= "<td class='dia_con_evento_calendario'><a href='{$_SESSION['appPath']}/{$tipo}/{$calendario['ano']}-{$calendario['mes']}-{$semana[$dia]['dia']}'>{$semana[$dia]['dia']}</a></td>";
                    else
                        $cuerpo .= "<td class='diasactuales_calendario'>{$semana[$dia]['dia']}</td>";
                } else {
                    $cuerpo .= "<td class='diasactuales_calendario'>&nbsp;</td>";
                }
            }
            $cuerpo .= "</tr>";
        }

        $html = "<table id='calendario'>{$cabecera}{$cuerpo}</table>";

        return $html;
    }

}

?>
