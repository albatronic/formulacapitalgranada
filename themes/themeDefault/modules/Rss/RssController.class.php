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
                
            case 'contenidos':
            default:
                $titulo = "Contenidos";
                $contenido = new GconContenidos();
                $rows = $contenido->cargaCondicion("Titulo,Resumen,UrlFriendly,PublishedAt", "1", "PublishedAt DESC {$limit}");
                unset($contenido);                
        }

        $this->values['rss'] = Feeds::getRss($titulo,$rows);

        header('Content-type: application/xml; charset="UTF-8"', true);
        //echo $xml;
        //exit;

        return array(
            'template' => $this->entity . "/index.xml.twig",
            'values' => $this->values,
        );
    }

}
