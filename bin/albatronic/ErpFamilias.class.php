<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE FAMILIAS/CATEGORIAS ERP
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 12-mayo-2013
 */
class ErpFamilias {

    /**
     * Devuelve un objeto familia
     * 
     * @param integer $idFamilia El id de la familia
     * @return \Familias
     */
    static function getFamilia($row) {

        $url = $row['UrlTarget'];
        $esInterna = ($url == '');

        if ($esInterna) {
            $url = $row['UrlFriendly'];
            $prefijo = $_SESSION['appPath'];
        } else {
            $prefijo = ($row['UrlIsHttps']) ? "https://" : "http://";
        }

        $url = $prefijo . $url . $row['UrlParameters'];

        $href = array('url' => $url, 'targetBlank' => $row['UrlTargetBlank']);

        $array = array(
            'IDFamilia' => $row['IDFamilia'],
            'Familia' => $row['Familia'],
            'Subtitulo' => $row['Subtitulo'],
            'Descripcion1' => $row['Descripcion1'],
            'Descripcion2' => $row['Descripcion2'],
            'Href' => $href,
        );

        return $array;
    }

    static function getObjetoFamilia($idFamilia) {
        return new Familias($idFamilia);
    }

    /**
     * Devuelve un array de objetos familias
     * 
     * @param integer $idFamiliaPadre El id de la familia padre. Por defecto 0 (las categorias)
     * @param integer $posicionInicio La posición de inicio (para obtener un rango)
     * @param integer $nItems El número de familias a devolver. Por defecto todas
     * @return array
     */
    static function getRangoFamilias($idFamiliaPadre = 0, $posicionInicio = 0, $nItems = 0) {

        $array = array();

        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia,Familia,Subtitulo,Descripcion1,Descripcion2,UrlTarget,UrlFriendly,UrlIsHttps,UrlParameters,UrlTargetBlank", "BelongsTo='{$idFamiliaPadre}'", "SortOrder ASC {$limite}");
        unset($familia);

        foreach ($rows as $row) {
            $array[] = self::getFamilia($row);
        }

        return $array;
    }

    /**
     * Devuelve un array de objetos familias que son categorias
     * 
     * @param integer $enPortada. 0 No portada, 1 Si portada, 2 Todas. Defecto Si portada
     * @param integer $posicionInicio A partir de que categoria
     * @param integer $nItems Número de categorias a devolver. Por defecto todas
     * @return array Array de categorias
     */
    static function getCategorias($enPortada = 1, $posicionInicio = 0, $nItems = 0) {

        $array = array();

        $nItems = ($nItems <= 0) ? 999999 : $nItems;
        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $filtro = "BelongsTo='0'";

        if (!in_array($enPortada, array(0, 1, 2)))
            $enPortada = 1;

        switch ($enPortada) {
            case '0':
                $filtro .= "AND MostrarPortada='0'";
                $orden = "SortOrder";
                break;
            case '1':
                $filtro .= "AND MostrarPortada='1'";
                $orden = "OrdenPortada";
                break;
            case '2':
                $orden = "SortOrder";
                break;
        }

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia,Familia,Subtitulo,Descripcion1,Descripcion2,UrlTarget,UrlFriendly,UrlIsHttps,UrlParameters,UrlTargetBlank", $filtro, "{$orden} ASC {$limite}");
        unset($familia);

        foreach ($rows as $row) {
            $array[] = self::getFamilia($row);
        }

        return $array;
    }

    /**
     * Devuelve un array con las familias de la categoría $idCategoria
     * 
     * @param integer $idCategoria El id de la categoría
     * @return array (Id,Value)
     */
    static function getFamilias($idCategoria) {

        $familia = new Familias();
        $array = $familia->cargaCondicion("IDFamilia as Id,Familia as Value", "BelongsTo='{$idCategoria}'");
        unset($familia);

        return $array;
    }

    /**
     * Devuelve un array de objetos fabricantes que están
     * relacionados con la categoría $idCategoria
     * 
     * @param integer $idCategoria El id de la categoría
     * @return array de objetos Fabricantes
     */
    static function getFabricantes($idCategoria) {

        $familia = new Familias($idCategoria);
        $array = $familia->getFabricantes();
        unset($familia);

        return $array;
    }

    /**
     * Devuelve un array de categorias y familias
     * 
     * @param boolean $enPortada Si es TRUE solo se muestran las categorias de portada
     * @param int $nItems Número máximo de categorías a mostrar. Por defecto todas.
     * @return array
     */
    static function getCategoriasFamilias($enPortada = true, $nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia,Familia,Subtitulo,Descripcion1,Descripcion2,UrlTarget,UrlFriendly,UrlIsHttps,UrlParameters,UrlTargetBlank", "BelongsTo='0' and MostrarPortada='{$enPortada}'", "OrdenPortada ASC {$limite}");
        unset($familia);

        foreach ($rows as $row) {

            $familia = new Familias();
            $rows1 = $familia->cargaCondicion("IDFamilia,Familia,Subtitulo,Descripcion1,Descripcion2,UrlTarget,UrlFriendly,UrlIsHttps,UrlParameters,UrlTargetBlank", "BelongsTo='{$row['IDFamilia']}' and MostrarPortada='{$enPortada}'");
            unset($familia);

            $array[$row['IDFamilia']]['categoria'] = self::getFamilia($row);

            foreach ($rows1 as $row1) {
                $array[$row['IDFamilia']]['familias'][] = self::getFamilia($row1);
            }
        }

        return $array;
    }

    /**
     * Devuelve un array paginado de artículos que pertenecen
     * a la categoría, familia o subfamilia $idCategoria
     * 
     * El array tiene dos elementos:
     * 
     * - articulos => Array de objetos articulos
     * - paginacion => array paginacion
     * 
     * @param integer $idCategoria El id de la categoría, familia o subfamilia
     * @param string $orgen El criterio de orden. Por defecto "SortOrder ASC"
     * @param integer $pagina El número de página
     * @param integer $nItems Número de items por página. Por defecto todos
     * @return array Array de artículos paginado
     */
    static function getArticulosPaginados($idCategoria, $orden, $pagina = 1, $nItems = 0) {

        $familia = new Familias($idCategoria);
        $nivelJerarquico = $familia->getNivelJerarquico();
        unset($familia);

        switch ($nivelJerarquico) {
            case 1:
                $campo = "IDCategoria";
                break;
            case 2:
                $campo = "IDFamilia";
                break;
            case 3:
                $campo = "IDSubfamilia";
                break;
        }

        if ($orden == '')
            $orden = "SortOrder ASC";

        $nPagina = ($pagina <= 0) ? 1 : $pagina;
        $filtro = "{$campo}='{$idCategoria}' and Vigente='1'";

        Paginacion::paginar("Articulos", $filtro, $orden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row) {
            $articulos[] = new Articulos($row['IDArticulo']);
        }

        return array(
            'articulos' => $articulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array sin paginar de artículos que pertenecen
     * a la categoría, familia o subfamilia $idCategoria y al fabricante $idFabricante
     * 
     * @param integer $idCategoria El id de la categoría, familia o subfamilia
     * @param integer $idFabricante El id del fabricante. Opcional
     * @param integer $nItems Número de items a devolver. Por defecto todos
     * @return array Array de artículos sin paginar
     */
    static function getArticulos($idCategoria, $idFabricante = '', $nItems = 0) {

        $array = array();

        $familia = new Familias($idCategoria);
        $nivelJerarquico = $familia->getNivelJerarquico();
        unset($familia);

        switch ($nivelJerarquico) {
            case 1:
                $campo = "IDCategoria";
                break;
            case 2:
                $campo = "IDFamilia";
                break;
            case 3:
                $campo = "IDSubfamilia";
                break;
        }

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "{$campo}='{$idCategoria}' and Vigente='1'";
        $filtro .= ($idFabricante != '') ? " and IDFabricante='{$idFabricante}'" : "";

        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("IDArticulo", $filtro, "SortOrder ASC {$limite}");
        unset($articulo);

        foreach ($rows as $row)
            $array[] = new Articulos($row['IDArticulo']);

        return $array;
    }

    /**
     * Devuelve un array de objetos articulos que están relacionados con
     * la categoria, familia o subfamilia indicada en $idCategoria
     * 
     * Esto se hace en base a la tabla CpanRelaciones.
     * 
     * Los artículos que se devuelven son aquellos que pertenecen a las
     * categorias, familias o subfamilias relacionadas con la indicada.
     * 
     * Si la indicada no tuviese relaciones, se devuelven los de la cateogría padre.
     * 
     * @param integer $idCategoria El id de la categoria, familia o subfamilia
     * @param integer $nItems El número de articulos a devolver por cada familia
     * @return array Array de objetos articulos
     */
    static function getArticulosRelacionados($idCategoria, $nItems = 5) {

        // Obtener el array de los objetos relacionadas
        $relacion = new CpanRelaciones();
        $familiasRelacionadas = $relacion->getObjetosRelacionados("Familias", $idCategoria);
        unset($relacion);

        // Si la categoria indicada no tiene relaciones, busco las
        // eventuales relaciones con la categoría padre a la indicada
        if (count($familiasRelacionadas) == 0) {
            $familia = self::getObjetoFamilia($idCategoria);
            if ($familia->getNivelJerarquico() > 1) {
                $idCategoria = $familia->getBelongsTo()->getIDFamilia();
                $relacion = new CpanRelaciones();
                $familiasRelacionadas = $relacion->getObjetosRelacionados("Familias", $idCategoria);
                unset($relacion);
            }
            unset($familia);
        }

        $nItems = ($nItems <= 0) ? 5 : $nItems;
        $array = array();

        // Para cada una de las familias relacionadas, obtengo sus $nItems artículos
        foreach ($familiasRelacionadas as $familia) {
            $arrayAux = self::getArticulos($familia->getPrimaryKeyValue(), '', $nItems);
            $array = array_merge($array, $arrayAux);
        }

        return $array;
    }

    /**
     * Genera el array con las categorias más visitados
     * 
     * 
     * Las categorias se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * El array tiene 2 elementos:
     * 
     * - titulo => la descripcion de la familia
     * - url => array(url => texto, targetBlank => boolean)
     * 
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional. (0=todos)
     * @return array Array con las categorias/familias/subfamilias
     */
    static function getMasVisitados($nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $categoria = new Familias();

        $rows = $categoria->cargaCondicion("IDFamilia", "", "NumberVisits DESC {$limite}");

        foreach ($rows as $row) {
            $categoria = new Familias($row['IDFamilia']);
            $array[] = array(
                'titulo' => $categoria->getFamilia(),
                'url' => $categoria->getHref(),
            );
        }
        unset($categoria);

        return $array;
    }

    /**
     * Devuelve un array con las propiedades y valores de la familia $idFamilia
     * 
     * El índice del array es el id de la propiedad. Cada elemento tiene:
     * 
     * - Titulo => el título de la propiedad
     * - Tipo => el tipo de propiedad (1: desplegable, 2: lista valores, 3: color)
     * - Valores => los valores posibles
     * 
     * @param int $idFamilia El id de la familia
     * @param boolean $filtrable True para obtener solo las propiedades filtrables. Por defecto todas.
     * @return array
     */
    static function getPropiedades($idFamilia, $filtrable = false) {

        $array = array();

        $filtro = "IDFamilia='{$idFamilia}'";
        $filtro .= ($filtrable) ? " AND Filtrable='1'" : "";

        $propiedades = new FamiliasPropiedades();
        $rows = $propiedades->cargaCondicion("IDPropiedad", $filtro);
        unset($propiedades);

        foreach ($rows as $row) {
            $propiedad = new Propiedades($row['IDPropiedad']);
            $array[$row['IDPropiedad']] = array(
                'Titulo' => $propiedad->getTitulo(),
                'Tipo' => $propiedad->getIDTipo()->getIDTipo(),
                'Valores' => $propiedad->getValores(),
            );
        }

        return $array;
    }

}

?>
