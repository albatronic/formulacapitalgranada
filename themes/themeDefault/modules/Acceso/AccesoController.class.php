<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @version 1.0 26-nov-2012
 */
class AccesoController extends ControllerProject {

    protected $entity = "Acceso";

    public function IndexAction() {

        /* MENSAJE */
        $this->values['mensaje'] = array(
            'acceso' => 'Nullam iaculis tortor id diam iaculis convallis. In porttitor mollis lobortis. Integer tempor malesuada nisl, vitae ultricies tellus sollicitudin hendrerit. Fusce tempor tellus sit amet odio scelerisque ut rutrum lectus hendrerit. Vestibulum semper commodo sagittis.',
        );


        return parent::IndexAction();
    }

}

?>
