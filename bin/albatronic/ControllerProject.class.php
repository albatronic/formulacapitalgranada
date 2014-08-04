<?php

/**
 * CONTROLADOR DE PROYECTO. EXTIENDE AL CONTROLADOR WEB
 * 
 * El constructor realiza las tareas comunes al proyecto como por ej.
 * construir la ruta de navegación y los menús
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 26-nov-2012
 */
class ControllerProject extends ControllerWeb {

    public function __construct($request) {

        parent::__construct($request);

        $this->values['firma'] = $this->getFirma();
        $this->values['ruta'] = $this->getRuta();
        $this->values['calendario'] = Calendario::getCalendario();
        $this->values['ustedEstaEn'] = $this->getUstedEstaEn();
        $this->values['menuCabecera'] = Menu::getMenuN(1,7);
        $this->values['menuLateral'] = Menu::getMenuDesplegable(2);
        $this->values['menuPie'] = Menu::getMenuN(3,8);
        
        $this->values['datosContacto'] = $this->varWeb['Pro']['globales'];    
        $this->values['redes'] = RedesSociales::getRedes();

        
        // El objeto widgets para que esté disponible en todas las vistas
        $this->values['widgets'] = new Widgets();           

        // GESTION DE COOKIES. El cartel debe mostrarse cada 7 días
        if (empty($_COOKIE["SESS_ID_CARTEL_COOKIE"])) {
            setcookie("SESS_ID_CARTEL_COOKIE", uniqid(time()), time() + (86400 * 7), "/");
        }
        $this->values["session_id"] = $_COOKIE["SESS_ID_CARTEL_COOKIE"];

    }

}