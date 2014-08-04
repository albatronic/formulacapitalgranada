<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE LOS ALBUMES
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 15-mar-2013
 */
class Albumes {
    
    /**
     * Devuelve un array con álbumes fotográficos
     * 
     * En el array habrá tantos elementos como álbumes devueltos.
     * 
     * Cada uno de estos elementos tiene la siguiente estructura:
     * 
     * - titulo => el título del álbum
     * - subtitulo => el subtitulo del álbum
     * - resumen => el resumen del álbum
     * - imagen => path a la imagen de diseño 1
     * - bloqueThumbnail => array(
     *      0 => array( 
     *              PathName => el path de la imagen
     *              Title => el titulo de la imagen (si ShowCaption = TRUE)
     *              PathNameThumbnail => el path del thumbnail
     *          )
     *      ....
     * )
     * - restosImagenes => array(
     *      0 => array( 
     *              PathName => el path de la imagen
     *              Title => el titulo de la imagen (si ShowCaption = TRUE)
     *              PathNameThumbnail => el path del thumbnail
     *          )
     *      ....
     *           
     * @param int $portada Si es menor a 0 se muestran los de SI portada y los de No portada.
     * Si es 0 se muestran solo los de NO portada. Si es mayor que 0 se muestran solo los de SI portada. Por defecto 1
     * @param int $idSeccion El id de la sección de álbumes. Un valor menor o igual a 0 indica todas las secciones. Por defecto todas (0)
     * @param int $nAlbumes El número de álbumes a devolver. Por defecto 1
     * @param int $nImagenes El número de imágenes que compondrá el bloque 'bloqueThumbnail'. Si hubiese más, el resto estarán en el bloque 'restoImagenes'. Por defecto 1
     * @return array Array de álbumes fotográficos
     */
    static function getAlbumes($portada = 1, $idSeccion = 0, $nAlbumes = 1, $nImagenes = 1) {

        $albumes = array();

        if ($nAlbumes <= 0)
            $nAlbumes = 999999;
        if ($nImagenes <= 0)
            $nImagenes = 999999;

        $filtro = ($idSeccion <= 0) ? "(1)" : "(IdSeccion='{$idSeccion}')";

        if ($portada > 0) {
            $filtro .= " AND (MostrarEnPortada='1')";
            $orden = "OrdenPortada ASC";
        } else
            $orden = "SortOrder ASC";

        $album = new AlbmAlbumes();
        $rows = $album->cargaCondicion("Id", $filtro, "{$orden} LIMIT {$nAlbumes}");
        unset($album);

        foreach ($rows as $key => $row) {
            $album = new AlbmAlbumes($row['Id']);

            $albumes[$key] = self::getAlbum($row['Id'], $nImagenes);
            $albumes[$key]['titulo'] = $album->getTitulo();
            $albumes[$key]['subtitulo'] = $album->getSubtitulo();
            $albumes[$key]['resumen'] = $album->getResumen();
            $albumes[$key]['autor'] = $album->getAutor();
            $albumes[$key]['imagen'] = ($album->getPathNameImagenN(2))?$album->getPathNameImagenN(2):$album->getPathNameImagenN(1);
        }
        unset($album);

        return $albumes;
    }

    /**
     * Devuelve un array con las imágenes del albúm fotográfico EXTERNO asociado 
     * a la entidad $entidad e $idEntidad
     * 
     * @param string $entidad El nombre de la entidad
     * @param int $idEntidad El id de la entidad
     * @param int $nImagenes El número de imagenes a devolver. Por defecto todas.
     * @return array
     *  
     * @example Para obtener los 2 primeros álbumes externos del servicio cuyo
     * id es 3: getAlbumExterno('ServServicios',3,2)
     */
    static function getAlbumExterno($entidad, $idEntidad, $nImagenes = 99999) {

        $array = array();

        if ($nImagenes <= 0)
            $nImagenes = 99999;

        $objeto = new $entidad($idEntidad);
        $albumExterno = $objeto->getIdAlbumExterno();
        if ($albumExterno->getId())
            $array = self::getAlbum($albumExterno->getId(), $nImagenes);
        unset($objeto);

        return $array;
    }

    /**
     * Devuelve un array con las imágenes del album fotográfico $idAlbum
     * 
     * El array tiene dos elementos:
     * 
     * - bloqueThumbnail: array del tipo galeria
     * - restoImagenes: array del tipo galeria
     * 
     * En el primer elemento habrá tantas imágenes como las indicadas en $nImagenes.
     * Si el album tuviese más imágenes, estas estarán en el elemento "restoImagenes"
     * 
     * @param int $idAlbum El id del álbum fotográfico
     * @param int $nImagenes El número de imágenes a devolver. Por defecto todas
     * @return array
     */
    static function getAlbum($idAlbum, $nImagenes = 99999) {

        $array = array();

        if ($nImagenes <= 0)
            $nImagenes = 99999;

        $array['bloqueThumbnail'] = self::getGaleria('AlbmAlbumes', $idAlbum, 0, $nImagenes);
        $array['restoImagenes'] = self::getGaleria('AlbmAlbumes', $idAlbum, $nImagenes);

        return $array;
    }

    /**
     * 
     * @param type $entidad
     * @param type $idEntidad
     * @param int $posicionInicio
     * @param int $nImagenes Número máximo de imágenes a devolver. Opcional (por defecto todas)
     * @return array
     */
    static function getGaleria($entidad, $idEntidad, $posicionInicio = 0, $nImagenes = 999999) {

        if ($posicionInicio < 0)
            $posicionInicio = 0;
        $nImagenes = ($nImagenes <= 0) ? 999999 : $nImagenes;

        $limite = "{$posicionInicio},{$nImagenes}";

        $dcto = new CpanDocs();
        $rows = $dcto->cargaCondicion("Id,PathName,Title,ShowCaption", "Entity='{$entidad}' and IdEntity='{$idEntidad}' and Type='galery' and IsThumbnail='0' and Idioma='{$_SESSION['idiomas']['actual']}'", "SortOrder ASC LIMIT {$limite}");

        foreach ($rows as $key => $row) {
            $thumbnail = $dcto->cargaCondicion("PathName", "BelongsTo='{$row['Id']}'");
            $rows[$key]['PathNameThumbnail'] = $thumbnail[0]['PathName'];
            if (!$row['ShowCaption'])
                unset($rows[$key]['Title']);
            unset($rows[$key]['Id']);
            unset($rows[$key]['ShowCaption']);
        }
        unset($dcto);

        return $rows;
    }

}

?>
