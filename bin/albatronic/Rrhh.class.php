<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE RECURSOS HUMANOS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática Albatronic, sl
 * @version 1.0 23-abril-2013
 */
class Rrhh {
    
    /**
     * Devuelve un objeto persona
     * 
     * @param integer $idPersona el id de la Persona
     * @return RrhhPersonal
     */
    static function getPersona($idPersona) {
        return new RrhhPersonal($idPersona);
    }
    
    /**
     * Devuelve un array paginado de objetos Personas
     * 
     * @param integer $nItems El número de personas a devolver. Por defecto todas
     * @return array Array de objetos personas
     */
    static function getPersonal($nItems = 99999) {
        
        $personas = array();
        
        $persona = new RrhhPersonal();
        $rows = $persona->cargaCondicion("Id");
        unset($persona);
        
        foreach ($rows as $row)
            $personas[] = self::getPersona($row['Id']);
        
        return $personas;
    }
    
    /**
     * Devuelve un array de objeto personas que pertenecen
     * al departamento $idDpto
     * 
     * @param integer $idDpto El id del departamento
     * @return array Array de objetos personas
     */
    static function getPersonalDpto($idDpto) {
        
        $personas = array();
        
        $persona = new RrhhPersonal();
        $rows = $persona->cargaCondicion("Id","IdDpto='{$idDpto}'");
        unset($persona);
        
        foreach ($rows as $row)
            $personas[] = self::getPersona($row['Id']);
        
        return $personas;
    }
    
    /**
     * Devuelve un array de objetos personas que son del puesto $idPuesto
     * 
     * @param integer $IdPuesto El id del puesto
     * @return array Array de objeto personas
     */
    static function getPersonalPuesto($IdPuesto) {
        
        $personas = array();
        
        $persona = new RrhhPersonal();
        $rows = $persona->cargaCondicion("Id","IdPuesto='{$IdPuesto}'");
        unset($persona);
        
        foreach ($rows as $row)
            $personas[] = self::getPersona($row['Id']);
        
        return $personas;
    }      
}

?>
