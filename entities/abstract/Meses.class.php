<?php

/**
 * Description of Meses del año
 * 
 * Idiomas implementados: Inglés, Francés, Italiano, Alemán, Español
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 20-feb-2013
 *
 */
class Meses extends Tipos {

    protected $tipos;

    public function __construct($IDTipo = null) {

        switch ($_SESSION['LANGUAGE']) {
            case 'en': // Inglés
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'January'),
                    array('Id' => '2', 'Value' => 'February'),
                    array('Id' => '3', 'Value' => 'March'),
                    array('Id' => '4', 'Value' => 'April'),
                    array('Id' => '5', 'Value' => 'May'),
                    array('Id' => '6', 'Value' => 'June'),
                    array('Id' => '7', 'Value' => 'July'),
                    array('Id' => '8', 'Value' => 'August'),
                    array('Id' => '9', 'Value' => 'September'),
                    array('Id' => '10', 'Value' => 'October'),
                    array('Id' => '11', 'Value' => 'November'),
                    array('Id' => '12', 'Value' => 'December'),
                );                
                break;
            
            case 'fr': // Francés
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Janvier'),
                    array('Id' => '2', 'Value' => 'Février'),
                    array('Id' => '3', 'Value' => 'Mars'),
                    array('Id' => '4', 'Value' => 'Avril'),
                    array('Id' => '5', 'Value' => 'Mai'),
                    array('Id' => '6', 'Value' => 'Juin'),
                    array('Id' => '7', 'Value' => 'Juillet'),
                    array('Id' => '8', 'Value' => 'Août'),
                    array('Id' => '9', 'Value' => 'September'),
                    array('Id' => '10', 'Value' => 'Octobre'),
                    array('Id' => '11', 'Value' => 'Novembre'),
                    array('Id' => '12', 'Value' => 'Décembre'),
                );                 
                break;
            
            case 'it': // Italiano
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Gennaio'),
                    array('Id' => '2', 'Value' => 'Febbraio'),
                    array('Id' => '3', 'Value' => 'Marzo'),
                    array('Id' => '4', 'Value' => 'Aprile'),
                    array('Id' => '5', 'Value' => 'Maggio'),
                    array('Id' => '6', 'Value' => 'Giugno'),
                    array('Id' => '7', 'Value' => 'Luglio'),
                    array('Id' => '8', 'Value' => 'Agosto'),
                    array('Id' => '9', 'Value' => 'Settembre'),
                    array('Id' => '10', 'Value' => 'Ottobre'),
                    array('Id' => '11', 'Value' => 'Novembre'),
                    array('Id' => '12', 'Value' => 'Dicembre'),
                );                  
                break;
            
            case 'de': // Alemán
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Januar'),
                    array('Id' => '2', 'Value' => 'Februar'),
                    array('Id' => '3', 'Value' => 'März'),
                    array('Id' => '4', 'Value' => 'April'),
                    array('Id' => '5', 'Value' => 'Mai'),
                    array('Id' => '6', 'Value' => 'Juni'),
                    array('Id' => '7', 'Value' => 'Juli'),
                    array('Id' => '8', 'Value' => 'August'),
                    array('Id' => '9', 'Value' => 'September'),
                    array('Id' => '10', 'Value' => 'Oktober'),
                    array('Id' => '11', 'Value' => 'November'),
                    array('Id' => '12', 'Value' => 'Dezember'),
                );                   
                break;
            
            default: // Español
                $this->tipos = array(
                    array('Id' => '1', 'Value' => 'Enero'),
                    array('Id' => '2', 'Value' => 'Febrero'),
                    array('Id' => '3', 'Value' => 'Marzo'),
                    array('Id' => '4', 'Value' => 'Abril'),
                    array('Id' => '5', 'Value' => 'Mayo'),
                    array('Id' => '6', 'Value' => 'Junio'),
                    array('Id' => '7', 'Value' => 'Julio'),
                    array('Id' => '8', 'Value' => 'Agosto'),
                    array('Id' => '9', 'Value' => 'Septiembre'),
                    array('Id' => '10', 'Value' => 'Octubre'),
                    array('Id' => '11', 'Value' => 'Noviembre'),
                    array('Id' => '12', 'Value' => 'Diciembre'),
                );
                break;
        }

        parent::__construct($IDTipo);
    }

}

?>
