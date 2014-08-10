<?php

/**
 * Description of SitemapController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @since 09.09.2014
 */
class SitemapController extends ControllerProject {

    protected $entity = "Sitemap";

    public function IndexAction() {

        $rows = array();

        $columnas = "UrlFriendly,DATE_FORMAT(ModifiedAt,'%Y-%m-%d') ModifiedAt,ChangeFreqSitemap,ImportanceSitemap";
        $orden = "PublishedAt DESC";
        $filtro = "ShowOnSitemap='1'";
        $limit = ($this->request[2] > 0 ) ? "limit {$this->request[2]}" : "";

        switch ($this->request['1']) {
            case 'index.xml':
                $rows[]['UrlFriendly'] = "/blog";
                $rows[]['UrlFriendly'] = "/noticias";
                $rows[]['UrlFriendly'] = "/eventos";
                $rows[]['UrlFriendly'] = "/wiki";
                $rows[]['UrlFriendly'] = "/categorias";
                $rows[]['UrlFriendly'] = "/productos";
                $rows[]['UrlFriendly'] = "/contenidos";
                $rows[]['UrlFriendly'] = "/secciones";
                $xml = Feeds::getSiteMapIndex($rows);
                break;

            case 'blog.xml':
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion($columnas, "{$filtro} AND BlogPublicar='1'", "{$orden} {$limit}");
                unset($contenido);
                $xml = Feeds::getSiteMap($rows);
                break;

            case 'noticias.xml':
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion($columnas, "{$filtro} AND NoticiaPublicar='1'", "{$orden} {$limit}");
                unset($contenido);
                $xml = Feeds::getSiteMap($rows);
                break;

            case 'eventos.xml':
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion($columnas, "{$filtro} AND EsEvento='1'", "{$orden} {$limit}");
                unset($contenido);
                $xml = Feeds::getSiteMap($rows);
                break;

            case 'wiki.xml':
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion($columnas, "{$filtro} AND EsWiki='1'", "{$orden} {$limit}");
                unset($contenido);
                $xml = Feeds::getSiteMap($rows);
                break;

            case 'categorias.xml':
                if (class_exists("Familias")) {
                    $familia = new Familias();
                    $rows = $familia->cargaCondicion($columnas, "{$filtro} AND BelongsTo='0'", "{$orden} {$limit}");
                    unset($familia);
                    $xml = Feeds::getSiteMap($rows);
                }
                break;

            case 'productos.xml':
                if (class_exists("Articulos")) {
                    $producto = new Articulos();
                    $rows = $producto->cargaCondicion($columnas, $filtro, "{$orden}  {$limit}");
                    unset($producto);
                    $xml = Feeds::getSiteMap($rows);
                }
                break;

            case 'contenidos.xml':
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion($columnas, $filtro, "{$orden} {$limit}");
                unset($contenido);
                $xml = Feeds::getSiteMap($rows);
                break;

            case 'secciones.xml':
            default:
                $seccion = new GconSecciones();
                $rows = $seccion->cargaCondicion($columnas, $filtro, "{$orden} {$limit}");
                unset($seccion);
                $xml = Feeds::getSiteMap($rows);
        }

        $this->values['sitemap'] = $xml;

        header("Content-Type: application/xml; charset=utf-8");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        //echo $xml;
        //exit;

        return array(
            'template' => $this->entity . "/index.xml.twig",
            'values' => $this->values,
        );
    }

}
