<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 01.02.2014 21:09:46
 */

/**
 * @orm:Entity(CpanTextos)
 */
class CpanTextos extends CpanTextosEntity {

    protected $Publish = '1';

    public function __toString() {
        return $this->getObservations();
    }

    /**
     * Devuelve un array con los textos comunes a todos los
     * controles y los del propio controlador indicado.
     * 
     * De tal manera que prevalecen los del controller sobre los
     * comunes en caso de solapamiento,
     * 
     * El índice del array es la palabra clave y el valor es el 
     * texto traducido.
     * 
     * @param string $controller El nombre del controlador
     * @param string $idioma El código de idioma. Por defecto el idioma en curso
     * @return array Array con los textos
     */
    function getTextos($controller, $idioma = '') {

        $array = array();

        if (!$idioma) {
            $codigoIdioma = $_SESSION['idiomas']['actual'];
            $idioma = $_SESSION['idiomas']['disponibles'][$codigoIdioma]['codigo'];
        }

        $filtroIdioma = "Lang='$idioma'";
        // Obtener los textos comunes a todos los controles
        $rowsComunes = $this->cargaCondicion("Clave,Observations", "Controller='' AND {$filtroIdioma}");
        foreach ($rowsComunes as $texto) {
            $array[$texto['Clave']] = $texto['Observations'];
        }
        // Obtener los textos del controlador indicado      
        $rowsController = $this->cargaCondicion("Clave,Observations", "Controller='{$controller}' AND {$filtroIdioma}");
        foreach ($rowsController as $texto) {
            $array[$texto['Clave']] = $texto['Observations'];
        }

        return $array;
    }

    /**
     * Carga datos en un array en funcion de una condicion where y orderBy
     *
     * @param string $columnas Relacion de las columnas seperadas por coma
     * @param string $condicion Condicion de filtrado que se utiliza en la clausula where (sin la cláusula WHERE)
     * @param string $orderBy Ordenacion, debe incluir la/s columna/s y el tipo ASC/DESC (sin la cláusula ORDER BY)
     * @return array $rows Array con las columnas devueltas
     */
    public function cargaCondicion($columnas = '*', $condicion = '1', $orderBy = '') {

        $this->conecta();

        if (is_resource($this->_dbLink)) {

            // Criterio de ordenación
            $orderBy = ($orderBy != '') ? "ORDER BY {$orderBy}" : "ORDER BY SortOrder";

            // Condición de vigencia
            $ahora = date("Y-m-d H:i:s");
            $filtro = "(Deleted='0') AND (Publish='1') AND (ActiveFrom<='{$ahora}') AND ( (ActiveTo>='{$ahora}') or (ActiveTo='0000-00-00 00:00:00') )";

            if ($condicion != '')
                $filtro .= " AND {$condicion}";

            $query = "SELECT {$columnas} FROM `{$this->_dataBaseName}`.`{$this->_tableName}` WHERE {$filtro} {$orderBy}";
            $this->_em->query($query); //echo $query,"<br/>";
            $this->setStatus($this->_em->numRows());

            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
        }

        unset($this->_em);

        return $rows;
    }
    /**
     * Devuelve un objeto cuyo valor de la columna $columna es igual a $valor
     *
     * @param string $columna El nombre de la columna
     * @param variant $valor El valor a buscar
     * @return this El objeto encontrado
     */
    public function find($columna, $valor) {

        $this->conecta();

        if (is_resource($this->_dbLink)) {

            $condicion = "({$columna}='{$valor}')";

            // Condición de vigencia
            $ahora = date("Y-m-d H:i:s");
            $condicion .= " AND Publish='1' AND (Deleted='0') AND (ActiveFrom<='{$ahora}') AND ( (ActiveTo>='{$ahora}') or (ActiveTo='0000-00-00 00:00:00') )";

            $query = "SELECT {$this->_primaryKeyName} FROM `{$this->_dataBaseName}`.`{$this->_tableName}` WHERE ({$condicion})";
            $this->_em->query($query);//echo $query;
            $this->setStatus($this->_em->numRows());
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
        }

        unset($this->_em);

        return new $this($rows[0][$this->_primaryKeyName]);
    }
}

?>