<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 02.02.2014 17:43:37
 */

/**
 * @orm:Entity(CpanPlantillas)
 */
class CpanPlantillas extends CpanPlantillasEntity {

    protected $Publish = '1';

    public function __toString() {
        return $this->getObservations();
    }

    /**
     * Devuelve el texto de la plantilla con los campos variables
     * reemplazados por sus valores en base al array $sustituir
     * 
     * @param string $controller El nombre del controlador
     * @param string $clave El nombre de la plantilla
     * @param array $sustituir Array opcional con los pares a sustituir
     * @param string $idioma El idioma, por defecto el que está en curso
     * @param string $separadorInicio La cadena de separación de inicio. Opcional. Por defecto {{
     * @param string $separadorFin La cadena de separación de fin. Opcional. Por defecto }}
     * @return string El texto de la plantilla
     */
    public function getPlantilla($controller, $clave, $sustituir = array(), $idioma = '', $separadorInicio = "{{", $separadorFin = "}}") {

        if (!$idioma) {
            $idioma = $_SESSION['idiomas']['disponibles'][$_SESSION['idiomas']['actual']]['codigo'];
        }

        $filtroIdioma = "Lang='$idioma'";

        // Obtener los textos del controlador indicado      
        $row = $this->cargaCondicion("Observations", "Controller='{$controller}' AND Clave='{$clave}' AND {$filtroIdioma}");
        $plantilla = $row[0]['Observations'];

        foreach ($sustituir as $key => $value) {
            $plantilla = str_replace($separadorInicio . $key . $separadorFin, $value, $plantilla);
        }

        return $plantilla;
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