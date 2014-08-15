<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 06-nov-2012
 *
 */
class IndexController extends ControllerProject {

    protected $entity = "Index";
    protected $nivel = "";

    public function IndexAction() {
        
        /* SLIDER DE IMAGENES */     
        $this->values['slider'] = Sliders::getSliders(1);
        
        // SERVICIOS DE LA HOME     
        $this->values['serviciosHome'] = Servicios::getServiciosAgrupados(true);  
        
        // POST DE LA HOME
        $this->values['posts'] = Blog::getArticulos(0,true);
        
        /* SLIDER NOTICIAS */
        //$this->values['carruselNoticias'] = Noticias::getNoticias(true); 
        
        /* EVENTOS ÚNICOS. */
        //$this->values['eventos'] = Eventos::getEventos('','', 3,2,true);

        /* LAS NOTICIAS MÁS LEIDAS */
        //$this->values['noticias'] = Noticias::getNoticiasMasLeidas(0, 2);

        /* LOS CONTENIDOS MAS VISITADOS */
        //$this->values['contenidosVisitados'] = Contenidos::getContenidosMasVisitados(6);

        /* PRESIDENTE */
        //$this->values['presidente'] = Contenidos::getContenido($this->varWeb['Pro']['staticContents'][0]);

        /* GALERIA FOTOS */
        //$this->values['galeriaFotos'] = Albumes::getAlbumes(1, "", 1, 5);

        /* VIDEO YOUTUBE */
        //$this->values['videoYoutube'] = Videos::getVideos(0,1,1);

        return parent::IndexAction();
    }

}