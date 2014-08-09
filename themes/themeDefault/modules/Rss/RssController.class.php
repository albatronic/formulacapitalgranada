<?php

/**
 * Description of RssController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @since 29.06.2014
 */
class RssController extends ControllerProject {

    protected $entity = "Rss";

    public function IndexAction() {

        $rows = array();

        $limit = ($this->request[2] > 0 ) ? "limit {$this->request[2]}" : "";

        switch ($this->request['1']) {
            case 'blog':
                $titulo = "Blog";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "BlogPublicar='1'", "PublishedAt DESC {$limit}");
                unset($contenido);
                break;
            case 'noticias':
                $titulo = "Noticias";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "NoticiaPublicar='1'", "PublishedAt DESC {$limit}");
                unset($contenido);
                break;
            case 'eventos':
                $titulo = "Eventos";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "EsEvento='1'", "PublishedAt DESC {$limit}");
                unset($contenido);
                break;
            case 'wiki':
                $titulo = "Wiki";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "EsWiki='1'", "PublishedAt DESC {$limit}");
                unset($contenido);
                break;
            case 'categorias':
                if (class_exists("Familias")) {
                    $titulo = "Categorias de Productos";
                    $familia = new Familias();
                    $rows = $familia->cargaCondicion("Familia as Titulo,Descripcion1 as Resumen,UrlFriendly,PublishedAt", "BelongsTo='0'", "PublishedAt DESC {$limit}");
                    unset($familia);
                }
                break;
            case 'productos':
                if (class_exists("Articulos")) {
                    $titulo = "Productos";
                    $producto = new Articulos();
                    $rows = $producto->cargaCondicion("Descripcion as Titulo,Resumen,UrlFriendly,PublishedAt", "1", "PublishedAt DESC  {$limit}");
                    unset($producto);
                }
                break;
            default:
                $titulo = "Contenidos";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "1", "PublishedAt DESC {$limit}");
                unset($contenido);
        }

        $items = "";
        foreach ($rows as $row) {

            $items .= "<item>"
                    . "<guid isPermaLink='true'>{$_SESSION['appUrl']}{$row['UrlFriendly']}</guid>"
                    . "<title>{$row['Titulo']}</title>"
                    . "<link>{$_SESSION['appUrl']}{$row['UrlFriendly']}</link>"
                    . "<description><![CDATA[{$row['Resumen']}]]></description>"
                    . "<pubDate>" . date('r', strtotime($row['PublishedAt'])) . "</pubDate>"
                    . "</item>";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'
                . "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">"
                . "<channel>"
                . "<title>RSS {$_SESSION['varWeb']['Pro']['globales']['empresa']} - {$titulo}</title>"
                . "<link>{$_SESSION['appUrl']}/rss</link>"
                . "<description>RSS {$_SESSION['varWeb']['Pro']['globales']['empresa']}</description>"
                . "<language>es</language>"
                . "<atom:link href=\"{$_SESSION['appUrl']}/rss\" rel=\"self\" type=\"application/rss+xml\" />"
                . $items
                . "</channel></rss>";

        $this->values['rss'] = $xml;

        header('Content-type: application/xml; charset="UTF-8"', true);
        //echo $xml;
        //exit;

        return array(
            'template' => $this->entity . "/index.xml.twig",
            'values' => $this->values,
        );
    }

}
