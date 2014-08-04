<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE SLIDERS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática ALBATRONIC, SL
 * @version 1.0 15-mar-2013
 */
class Sliders {

    /**
     * Devuelve un array con SLIDERS.
     * 
     * Están ordenados ASCEDENTEMENTE por Id.
     * 
     * Si el registro de slider existe pero no tiene imagen de diseño 1
     * o, teniéndola no está marcada publicar, no se tendrá en cuenta.
     * 
     * El array tiene 5 elementos:
     * 
     * - titulo => el titulo del slider
     * - subtitulo => el subtitulo del slider
     * - resumen => el resumen del slider si MostrarTextos = TRUE
     * - url => array(url => texto, targetBlank => boolean)
     * - imagen => Path de la imagen de diseño 1
     * 
     * @param int $idZona El id de la zona de slider a filtrar. Opcional. Defecto la primera que encuentre
     * @param int $tipo El tipo de sliders. Opcional. Por defecto el tipo 0. Valores posibles en entities/abstract/TiposSliders.class.php
     * @param int $nItems Número máximo de sliders a devolver. Opcional. Defecto todos
     * @return array Array de sliders
     */
    static function getSliders($idZona = '*', $tipo = 0, $nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : $nItems;

        // Valido el tipo de slider. Si no es correcto lo pongo a tipo 0 (variable)
        $tipoSlider = new TiposSliders($tipo);
        if ($tipoSlider->getIDTipo() == null)
            $tipo = 0;

        $where = ($idZona == '*') ? "(1)" : "s.IdZona='{$idZona}'";
        $where .= " AND s.IdTipo='{$tipo}'";

        $em = new EntityManager("");
        $select = "select s.Id,s.Titulo,s.Subtitulo,s.Resumen,s.MostrarTextos,s.Entidad,s.IdEntidad,s.UrlTarget,s.UrlIsHttps,s.UrlParameters,s.UrlTargetBlank,d.PathName as imagen
                    from SldSliders s 
                    left join CpanDocs d on s.Id=d.IdEntity and d.Entity='SldSliders' and d.Type='image1' and d.IsThumbnail='0' and d.Publish='1'";
        $rows = $em->getResult("s", $select, $where, "", $limite);

        foreach ($rows as $row) {
            // No se tienen en cuenta los sliders que no tienen imagen de diseño 1
            if ($row['imagen']) {

                if ($row['MostrarTextos'] === '0') {
                    $titulo = '';
                    $subtitulo = '';
                    $resumen = '';
                } else {
                    $titulo = $row['Titulo'];
                    $subtitulo = $row['Subtitulo'];
                    $resumen = $row['Resumen'];
                }

                if ($row['Entidad'] != '') {
                    $objetoEnlazado = new $row['Entidad']($row['IdEntidad']);
                    $href = $objetoEnlazado->getHref();
                    unset($objetoEnlazado);
                } elseif ($row['UrlTarget'] != '') {
                    $prefijo = ($row['UrlIsHttps']) ? "https://" : "http://";
                    $url = $prefijo . $row['UrlTarget'] . $row['UrlParameters'];
                    $href = array('url' => $url, 'targetBlank' => $row['UrlTargetBlank']);
                } else {
                    $href = array();
                }

                $array[] = array(
                    'titulo' => $titulo,
                    'subtitulo' => $subtitulo,
                    'resumen' => $resumen,
                    'url' => $href,
                    'imagen' => $row['imagen'],
                );
            }
        }
        
        return $array;
    }

}

?>
