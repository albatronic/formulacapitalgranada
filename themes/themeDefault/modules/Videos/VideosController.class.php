<?php

/**
 * Description of VideosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 26-nov-2012
 */
class VideosController extends ControllerProject {

    var $entity = "Videos";

    public function IndexAction() {

         // El número de vídeos por fila.
        $nItemsFila = 3;
        
        $array = array();        
        
        // Obtener todos los videos de la primera seccion.
        $albumes = Videos::getVideos(1,-1);
        
        $fila = 0;
        foreach($albumes as $key=>$album) {
            if ($key % $nItemsFila == 0) $fila += 1;
            $array[$fila][] = $album;
        }
        
        $this->values['galeriaVideos'] = $array;       
        
        return parent::IndexAction();
    }

    public function ShowItemAction() {

        $video = new VidVideos($this->request['IdEntity']);
        
        /* VIDEO YOUTUBE */
        $this->values['video'] = array(
            'titulo' => $video->getTitulo(),
            'subtitulo' => $video->getSubtitulo(),
            'embed' => $video->getUrlVideo(),
            'autor' => $video->getAutor(),
            'resumen' => $video->getResumen(),
            'tipo' => $video->getIdTipo()->getDescripcion(),
        );


        return array(
            "template" => $this->entity . "/videoIndividual.html.twig",
            "values" => $this->values,
        );
    }

}

?>
