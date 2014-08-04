<?php

/**
 * Define los días de la semana de Lunes (1) a Domingo (7)
 * 
 * Los idiomas disponibles son: Español, Inglés, Francés, Italiano, Alemán
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 20-feb-2013
 *
 */
class DiasSemana extends Tipos {

    protected $tipos;

    public function __construct($IDTipo = null) {

        switch ($_SESSION['LANGUAGE']) {

            case 'en': // Inglés
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Monday'),
                    array('Id' => '2', 'Value' => 'Tuesday'),
                    array('Id' => '3', 'Value' => 'Wednesday'),
                    array('Id' => '4', 'Value' => 'Thursday'),
                    array('Id' => '5', 'Value' => 'Friday'),
                    array('Id' => '6', 'Value' => 'Saturday'),
                    array('Id' => '7', 'Value' => 'Sunday'),
                );
                break;

            case 'fr': // Francés
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Lundi'),
                    array('Id' => '2', 'Value' => 'Mardi'),
                    array('Id' => '3', 'Value' => 'Mercredi'),
                    array('Id' => '4', 'Value' => 'Jeudi'),
                    array('Id' => '5', 'Value' => 'Vendredi'),
                    array('Id' => '6', 'Value' => 'Samedi'),
                    array('Id' => '7', 'Value' => 'Dimanche'),
                );
                break;

            case 'it': // Italiano
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Lunedì'),
                    array('Id' => '2', 'Value' => 'Martedì'),
                    array('Id' => '3', 'Value' => 'Mercoledì'),
                    array('Id' => '4', 'Value' => 'Giovedì'),
                    array('Id' => '5', 'Value' => 'Venerdì'),
                    array('Id' => '6', 'Value' => 'Sabato'),
                    array('Id' => '7', 'Value' => 'Domenica'),
                );
                break;

            case 'de': // Alemán
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Montag'),
                    array('Id' => '2', 'Value' => 'Dienstag'),
                    array('Id' => '3', 'Value' => 'Mittwoch'),
                    array('Id' => '4', 'Value' => 'Donnerstag'),
                    array('Id' => '5', 'Value' => 'Freitag'),
                    array('Id' => '6', 'Value' => 'Samstag'),
                    array('Id' => '7', 'Value' => 'Sonntag'),
                );
                break;

            default: // Español
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Lunes'),
                    array('Id' => '2', 'Value' => 'Martes'),
                    array('Id' => '3', 'Value' => 'Miercoles'),
                    array('Id' => '4', 'Value' => 'Jueves'),
                    array('Id' => '5', 'Value' => 'Viernes'),
                    array('Id' => '6', 'Value' => 'Sabado'),
                    array('Id' => '7', 'Value' => 'Domingo'),
                );
                break;
        }

        parent::__construct($IDTipo);
    }

}

?>
