<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 12.03.2013 21:48:35
 */

/**
 * @orm:Entity(RrhhDptos)
 */
class RrhhDptos extends RrhhDptosEntity {

    public function __toString() {
        return $this->getTitulo();
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'Titulo';
        return parent::fetchAll($column, $default);
    }

    /**
     * Devuelve un array con el personal correspondiente
     * al departamento indicado, o en su defecto al departamento actual.
     *
     * Si se indica $entidadRelacionada e $idEntidadRelacionada, se añade un elmento más que indica
     * si cada persona está relacionada con $entidadRelacionada e $idEntidadRelacionada
     *
     * El array tiene los siguientes elementos:
     * 
     * - Id: El id de la persona
     * - Value: Apellidos y Nombre de la persona
     * - PrimaryKeyMD5: la primarykey MD5
     * - Publish: TRUE/FALSE
     * - estaRelacionado: El id de la eventual relacion
     * 
     * @param integer $idDpto El id del departamento
     * @param string $idEntidadRelacionada La entidad con la que existe una posible relación
     * @param integer $idEntidadRelacionada El id de entidad con la que existe una posible relación
     * @return array Array Id, Value de personas
     */
    public function getPersonal($idDpto = '', $entidadRelacionada = '', $idEntidadRelacionada = '') {

        if ($idDpto == '')
            $idDpto = $this->Id;

        $personal = new RrhhPersonal();
        $personas = $personal->cargaCondicion('Id as Id,CONCAT(Apellidos," ",Nombre) as Value,PrimaryKeyMD5,Publish', "IdDpto='{$idDpto}'", "SortOrder ASC");
        unset($personal);

        if ($entidadRelacionada) {
            foreach ($personas as $key => $personal) {
                $relacion = new CpanRelaciones();
                $personas[$key]['estaRelacionado'] = $relacion->getIdRelacion($entidadRelacionada, $idEntidadRelacionada, 'RrhhPersonal', $personal['Id']);
            }
            unset($relacion);
        }
        return $personas;
    }

    /**
     * Devuelve un array con el árbol de departamentos y personal (empleados)
     * 
     * Si se indica valor para el parámetro $idContenidoRelacionado, en el array
     * de contenidos se incluirá un elemento booleano que indica si cada contenido
     * está relacionado con el contenido cuyo valor es el parámetro.
     * 
     * El índice del array contiene el valor de la primaryKeyMD5 de cada sección y la estructura es:
     * 
     * - id => el id de la seccion
     * - titulo => el titulo de la seccion
     * - nivelJerarquico => el nivel jerárquico dentro del árbol de secciones
     * - publish => Publicar TRUE/FALSE
     * - belongsTo => El id del padre al que pertenece
     * - nHijos => El número de dptos hijos
     * - hijos => array de dptos hijos (belongsTo) con la misma estructura
     * - nEmpleados => el número de empleados que posee el dpto
     * - empleados => array de empleados del dpto
     * 
     * @param boolean $conEmpleados
     * @param string $entidadRelacionada
     * @param integer $idEntidadRelacionada 
     * @return array Array de departamentos
     */
    public function getArbolHijos($conEmpleados = FALSE, $entidadRelacionada = '', $idEntidadRelacionada = '') {

        $arbol = array();

        $objeto = new $this();
        $rows = $objeto->cargaCondicion("Id,PrimaryKeyMD5,NivelJerarquico,Publish,BelongsTo", "BelongsTo='0'", "SortOrder ASC");
        unset($objeto);

        foreach ($rows as $row) {
            $objeto = new $this($row['Id']);
            $arrayEmpleados = ($conEmpleados) ? $this->getPersonal($row['Id'], $entidadRelacionada, $idEntidadRelacionada) : array();
            $arrayHijos = $objeto->getHijos('', $conEmpleados, $entidadRelacionada, $idEntidadRelacionada);
            $arbol[$row['PrimaryKeyMD5']] = array(
                'id' => $row['Id'],
                'titulo' => $objeto->getTitulo(),
                'nivelJerarquico' => $row['NivelJerarquico'],
                'publish' => $row['Publish'],
                'belongsTo' => $row['BelongsTo'],
                'nHijos' => count($arrayHijos),
                'hijos' => $arrayHijos,
                'nEmpleados' => count($arrayEmpleados),
                'empleados' => $arrayEmpleados,
            );
            if ($entidadRelacionada) {
                $relacion = new CpanRelaciones();
                $arbol[$row['PrimaryKeyMD5']]['estaRelacionado'] = $relacion->getIdRelacion($entidadRelacionada, $idEntidadRelacionada, 'RrhhDptos', $row['Id']);
            }
        }

        unset($objeto);
        return $arbol;
    }

    /**
     * Genera el árbol genealógico con las entidades hijas de la
     * entidad $idPadre.
     * 
     * El árbol se genera de forma recursiva sin límite de profundidad.
     * 
     * El array lleva valor únicamente en el índice, y dicho valor es el
     * id de las entidades.
     * 
     * @param integer $idPadre El id de la entidad padre
     * @return array
     */
    public function getHijos($idPadre = '', $conEmpleados = FALSE, $entidadRelacionada = '', $idEntidadRelacionada = '') {

        if ($idPadre == '')
            $idPadre = $this->getPrimaryKeyValue();

        $this->getChildrens($idPadre, $conEmpleados, $entidadRelacionada, $idEntidadRelacionada);
        return $this->_hijos[$idPadre];
    }

    /**
     * Generar un árbol genealógico con las entidades hijas
     * de la entidad cuyo id es $idPadre
     *
     * @param integer $idPadre El id de la entidad padre
     * @return array Array con los objetos hijos
     */
    private function getChildrens($idPadre, $conEmpleados, $entidadRelacionada, $idEntidadRelacionada) {

        // Obtener todos los hijos del padre actual
        $hijos = $this->cargaCondicion('Id,PrimaryKeyMD5,NivelJerarquico,Publish,BelongsTo', "BelongsTo='{$idPadre}'", "SortOrder ASC");

        foreach ($hijos as $hijo) {
            $aux = new $this($hijo['Id']);
            $arrayEmpleados = ($conEmpleados) ? $this->getPersonal($hijo['Id']) : array();
            $arrayHijos = $this->getChildrens($hijo['Id'], $conEmpleados, $entidadRelacionada, $idEntidadRelacionada);
            $this->_hijos[$idPadre][$hijo['PrimaryKeyMD5']] = array(
                'id' => $hijo['Id'],
                'titulo' => $aux->getTitulo(),
                'nivelJerarquico' => $hijo['NivelJerarquico'],
                'publish' => $hijo['Publish'],
                'belongsTo' => $hijo['BelongsTo'],
                'nHijos' => count($arrayHijos),
                'hijos' => $arrayHijos,
                'nEmpleados' => count($arrayEmpleados),
                'empleados' => $arrayEmpleados,
            );
            if ($entidadRelacionada) {
                $relacion = new CpanRelaciones();
                $this->_hijos[$idPadre][$hijo['PrimaryKeyMD5']]['estaRelacionado'] = $relacion->getIdRelacion($entidadRelacionada, $idEntidadRelacionada, 'RrhhDptos', $hijo['Id']);
            }
            unset($hijo);
        }

        return $this->_hijos[$idPadre];
    }
    
}

?>