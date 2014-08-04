<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE ARTICULOS ERP
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 12-mayo-2013
 */
class ErpArticulos {

    /**
     * Devuelve un array con datos básicos del artículo
     * @param array Array articulo
     * @return array
     */
    static function getArticulo($row) {

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
            'IDArticulo' => $row['IDArticulo'],
            'Codigo' => $row['Codigo'],
            'Descripcion' => $row['Descripcion'],
            'Subtitulo' => $row['Subtitulo'],
            'Resumen' => $row['Resumen'],
            'Href' => $href,
        );

        return $array;
    }

    /**
     * Devuelve un objeto artículo
     * @param integer $idArticulo El id del articulo
     * @return \Articulos
     */
    static function getObjetoArticulo($idArticulo) {
        return new Articulos($idArticulo);
    }

    /**
     * Devuelve un array en el detalle técnico del artículo $idArticulo
     * 
     * El array tiene tantos items como propiedades técnicas
     * y cada item tiene dos elementos:
     * 
     * - titulo: el título de la propiedad
     * - valor: el valor de la propiedad
     * 
     * @param int $idArticulo El id del artículo
     * @return array
     */
    static function getDetalleTecnico($idArticulo) {

        $array = array();

        $em = new EntityManager("");
        $select = "SELECT p.Titulo, v.Valor FROM ErpArticulosPropiedades ap
            LEFT JOIN ErpPropiedades p ON ap.IDPropiedad = p.Id
            LEFT JOIN ErpPropiedadesValores v ON ap.IDValor = v.Id";
        $where = "ap.IDArticulo =  '{$idArticulo}'";
        $rows = $em->getResult("ap", $select, $where);

        foreach ($rows as $row) {
            $array[] = array(
                'titulo' => $row['Titulo'],
                'valor' => $row['Valor'],
            );
        }

        return $array;
    }

    static function getArticulosPaginadosUsuario($idUsuario = '', $orden = '', $nPagina = '', $nItems = '') {

        if ($idUsuario == '')
            $idUsuario = $_SESSION['usuarioWeb']['id'];

        $var = CpanVariables::getVariables("Web", "Mod", "Articulos");

        if ($orden == '')
            $orden = $var['especificas']['CriterioOrden'];
        if ($orden == '')
            $orden = "PublishedAt DESC";

        if ($nPagina <= 0)
            $nPagina = 1;

        if ($nItems <= 0)
            $nItems = $var['especificas']['NumArticulosListado'];
        if ($nItems <= 0)
            $nItems = 4;

        $filtro = "IDCliente='{$idUsuario}' and Vigente='1'";

        Paginacion::paginar("Articulos", $filtro, $orden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $articulos[] = new Articulos($row['IDArticulo']);

        return array(
            'articulos' => $articulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array de objetos Articulos que estén en alguno de
     * los estados indicados en el array $estados
     * 
     * @param array $estados Array de estados
     * @param integer $nItems Número de artículos a devolver. Por defecto todos
     * @return array Array de objetos Articulos
     */
    static function getArticulosEstado($estados = array(), $nItems = 0) {

        $array = array();

        if (is_array($estados)) {
            foreach ($estados as $estado) {
                for ($i = 1; $i <= 5; $i++)
                    $filtro .= "(IDEstado{$i}='{$estado}') OR";
            }
            $filtro = substr($filtro, -3);
        } else
            $filtro = "";

        $limite = ($nItems > 0) ? "LIMIT {$nItems}" : "";


        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("*", $filtro, "SortOrder ASC {$limite}");
        unset($articulo);

        foreach ($rows as $row) {
            $array[] = self::getArticulo($row);
        }

        return $array;
    }

    /**
     * Devuelve un array de objetos artículos que
     * pertenecen a la zona $zona y al controlador $controller
     * 
     * Si no se indica zona, se devuelven todos los artículos agrupados por zonas. El array
     * tendrán tantos elementos como zonas, y a su vez cada elemento tendrás tantos
     * subelementos como artículos haya en la zona
     * 
     * Si se indica zona, los elementos del array serán directamente los
     * artículos, sin agrupar previamente por zona.
     * 
     * @param string $controller El nombre del controlador.
     * @param string $zona El codigo de la zona.
     * @param string $filtroAdicional
     * @return array Array de objetos artículos
     */
    static function getArticulosZona($controller, $zona, $filtroAdicional = '1') {

        $array = array();

        $controller = ucwords($controller);

        $filtroZona = ($zona == '') ? "1" : "Zona='{$zona}'";

        $zonas = new CpanEsqueletoWeb();
        $reglas = $zonas->cargaCondicion("Id,Zona,NItems,ItemsPagina", "Controller='{$controller}' AND {$filtroZona}", "SortOrder ASC");
        unset($zonas);

        $ordenArticulos = new OrdenesArticulos();
        foreach ($reglas as $regla) {
            $articulos = $ordenArticulos->getArticulos($regla['Id'], $regla['NItems'], $filtroAdicional);

            foreach ($articulos as $articulo) {
                if ($zona === '') {
                    $array[$regla['Zona']][$articulo->getIDArticulo()] = $articulo;
                } else {
                    $array[$articulo->getIDArticulo()] = $articulo;
                }
            }
        }
        unset($ordenArticulos);

        return $array;
    }

    /**
     * Devuelve $nItems objetos articulos que están relacionados
     * con el articulo cuyo id es $idArticulo.
     * 
     * EL criterio que se sigue para relacionar es en base a la familia
     * y si no tiene familia en base a la categoria.
     * 
     * @param integer $idArticulo EL id de articulo
     * @param integer $nItems Número de artículos a devolver
     * @return array Array con objetos articulos
     */
    static function getArticulosRelacionados($idArticulo, $nItems) {

        $articulo = self::getObjetoArticulo($idArticulo);
        $idFamilia = $articulo->getIDFamilia()->getIDFamilia();
        if (!$idFamilia) {
            $idFamilia = $articulo->getIDCategoria()->getIDFamilia();
        }
        unset($articulo);

        return ErpFamilias::getArticulosRelacionados($idFamilia, $nItems);
    }

    /**
     * Devuelve los artículos más visitados
     * 
     * @param integer $nItems El número de artículos a devolver. Por defecto 10
     * @return array de \Articulos
     */
    static function getLosMasVistos($nItems = 10) {

        $array = array();

        $limite = ($nItems > 0) ? "limit {$nItems}" : "";

        $articulos = New Articulos();
        $rows = $articulos->cargaCondicion("*", "Vigente='1'", "NumberVisits DESC {$limite}");
        unset($articulos);

        foreach ($rows as $row) {
            $array[] = self::getArticulo($row);
        }

        return $array;
    }

}

?>
