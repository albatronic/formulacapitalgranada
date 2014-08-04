<?php

/**
 * Description of GaleriaController
 *
 * @author Administrador
 */
class GaleriaController extends ControllerProject {

    var $entity = "Galeria";

    public function IndexAction() {

        /* GALERIA IMAGENES */
        
        // El número de álbumes por fila.
        $nItemsFila = 3;
        
        $array = array();        
        
        $albumes = Albumes::getAlbumes(-1,0,999,999);
        
        $fila = 0;
        foreach($albumes as $key=>$album) {
            if ($key % $nItemsFila == 0) $fila++;
            $array[$fila][] = $album;
        }
        
        $this->values['galeriaImagenes'] = $array;

        return parent::IndexAction();
    }

}

?>
