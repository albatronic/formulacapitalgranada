<?php

/**
 * CLASE QUE IMPLEMENTA LA CAPA DE ACCESO A DATOS.
 * LOS PARAMETROS NECESARIOS PARA LA CONEXION SE DEFINEN EN EL ARCHIVO
 * config/config.xml O EN EL INDICADO EN EL SEGUNDO PARAMETRO DEL CONSTRUCTOR
 *
 * SE ADMITEN DIFERENTES MOTORES DE BASE DE DATOS. ACTUALMENTE ESTAN
 * IMPLEMENTADOS PARA MYSQL, MSSQL E INTERBASE.
 *
 * SI LA CONEXION ES EXITOSA self::$dbLinkInstance TENDRA VALOR,
 * EN CASO CONTRARIO ALMACENA EL MENSAJE DE ERROR PRODUCIDO QUE SE
 * PUEDE CONOCER CON EL METODO getError()
 */
class EntityManager {

    /**
     * Fichero de configuracion de conexiones por defecto
     * Es el fichero que se utilizará si no se indica otro en la
     * llamada al constructor.
     * @var string
     */
    private $file = "config/config.yml";
    public static $dbLinkInstance = null;
    public static $currentDbLink = null;
    public static $dbEngine = null;
    public static $host = null;
    public static $user = null;
    public static $password = null;
    public static $dataBase = null;
    public static $conection = array();
    private $result = null;
    private $affectedRows = null;

    /**
     * Guardar el eventual error producido en la conexión
     * @var array
     */
    private $error = array();

    /**
     * Estable la conexion a la base de datos.
     * Abre el fichero de configuracion '$fileConfig', o en su defecto config/config.yml
     * y lee el nodo $conection donde se definen los parametros de conexion.
     * 
     * Si no se indica valor para el parámetro $conection, se tomarán los valores
     * de la primera conexión definida en el archivo de configuración. De esta forma, y en el caso
     * de trabajar con una sola base de datos, no es necesario indicar el nombre de conexión para
     * cada tabla en el modelo de datos.
     *
     * En entorno de desarrollo los parámetros de conexión se fuerzan a:
     *
     *      user    =   $conection
     *      password=   $conection
     *      dataBase=   $conection
     *
     * 
     * Si la conexion es exitosa, getDblink() devolvera valor y si no getError() nos indica
     * el error producido.
     *
     * @param string $conection Nombre de la conexion, opcional
     * @param string $fileConfig Nombre del fichero de configuracion, opcional
     */
    public function __construct($conection, $fileConfig = '') {

        //if (is_null(self::$dbLinkInstance)) {
        if (is_array($conection)) {
            self::$dbEngine = $conection['dbEngine'];
            self::$host = $conection['host'];
            self::$user = $conection['user'];
            self::$password = $conection['password'];
            self::$dataBase = $conection['database'];
            if (is_null(self::$dbLinkInstance)) {
                $this->conecta();
            }
        } else {
            if (count(self::$conection) == 0) {
                if ($fileConfig == '') {
                    $fileConfig = $_SERVER['DOCUMENT_ROOT'] . $_SESSION['appPath'] . "/" . $this->file;
                }
                if (file_exists($fileConfig)) {
                    $yaml = sfYaml::load($fileConfig);
                    // Si no se ha indicado el nombre de la conexión, se tomara la primera
                    if ($conection == '')
                        list($conection, $nada) = each($yaml['config']['conections']);
                    self::$conection = $yaml['config']['conections'][$conection];
                } else {
                    die("EntityManager []: ERROR AL LEER EL ARCHIVO DE CONFIGURACION. " . $fileConfig . " NO EXISTE\n");
                }
            }
            self::$dbEngine = self::$conection['dbEngine'];
            self::$host = self::$conection['host'];
            self::$user = self::$conection['user'];
            self::$password = self::$conection['password'];
            self::$dataBase = self::$conection['database'];
            if (is_null(self::$dbLinkInstance)) {
                $this->conecta();
            }
        }
    }

    /**
     * Conecta a la base de datos con los parametros de conexión indicados
     * en config/config.yml.
     * Si la conexion es exitosa self::$dbLinkInstance tendrá valor en caso contrario,
     * $this->error tendra el mensaje de error.
     */
    private function conecta() {

        switch (self::$dbEngine) {
            case 'mysql':
                self::$dbLinkInstance = mysql_connect(self::$host, self::$user, self::$password);
                if (is_resource(self::$dbLinkInstance)) {
                    mysql_select_db(self::$dataBase, self::$dbLinkInstance);
                }
                break;

            case 'mssql':
                self::$dbLinkInstance = mssql_connect(self::$host, self::$user, self::$password);
                if (is_resource(self::$dbLinkInstance)) {
                    mssql_select_db(self::$dataBase, self::$dbLinkInstance);
                }
                break;

            case 'interbase':
                self::$dbLinkInstance = ibase_connect(self::$host, self::$user, self::$password);
                break;
            default:
                $this->error[] = "EntityManager [conecta]: Conexión no realizada. No se ha indicado el tipo de base de datos. " . mysql_errno() . " " . mysql_error();
        }

        if (is_null(self::$dbLinkInstance)) {
            $this->error[] = "EntityManager [conecta]: No se pudo conectar " . self::$host . ":" . self::$dataBase . "Error: " . mysql_error();
        }
    }

    /**
     * Cierra la conexión con la base de datos
     */
    public function desConecta() {
        /**
          if (is_resource(self::$dbLinkInstance)) {
          switch (self::$dbEngine) {
          case 'mysql':
          mysql_close(self::$dbLinkInstance);
          break;
          case 'mssql':
          mssql_close(self::$dbLinkInstance);
          break;
          case 'interbase':
          ibase_free_result($this->result);
          ibase_close(self::$dbLinkInstance);
          break;
          default:
          $this->error[] = "EntityManager [desConecta]: Desconexión no realizada. No se ha indicado el tipo de base de datos";
          }
          }
         */
    }

    /**
     * Ejecuta un query sobre la conexion existente. Si se produce algun error
     * se puede consultar con getError().
     * @param string Sentencia sql
     * @return result
     */
    public function query($query) {
        $this->result = null;

        switch (self::$dbEngine) {
            case 'mysql':
                //mysql_select_db($this->getdataBase());
                $this->result = mysql_query($query, self::$dbLinkInstance);
                //$fp = fopen("log/queries.sql", "a");
                //fwrite($fp, date("Y-m-d H:i:s") . "\t" . $query . "\n");
                //fclose($fp);
                if (!$this->result)
                    $this->setError("query");
                else
                    $this->affectedRows = mysql_affected_rows(self::$dbLinkInstance);
                break;

            case 'mssql':
                //mssql_select_db($this->dataBase);
                $this->result = mssql_query($query, self::$dbLinkInstance);
                if (!$this->result)
                    $this->setError("query");
                else
                    $this->affectedRows = mysql_affected_rows(self::$dbLinkInstance);
                break;

            case 'interbase':
                $query = str_replace("`", "", $query);
                $this->result = ibase_query(self::$dbLinkInstance, $query);
                if (!$this->result)
                    $this->setError("query");
                else
                    $this->affectedRows = ibase_affected_rows(self::$dbLinkInstance);
                break;

            default:
                $this->setError("query", "No se ha indicado el tipo de base de datos");
        }
        return $this->result;
    }

    /**
     * Carga datos en un array desde la BD.
     * El primer elemento del array tiene el indice 0. Cada elemento es a su vez
     * otro array ASOCIATIVO.
     * Este metodo debe ser llamado despues del metodo query().
     * Si se produce algun error se puede consultar con getError().
     * @return array Las filas encontradas
     */
    public function fetchResult() {
        $rows = array();

        switch (self::$dbEngine) {
            case 'mysql':
                while ($row = mysql_fetch_array($this->result, MYSQL_ASSOC))
                    $rows[] = $row;
                break;

            case 'mssql':
                while ($row = mssql_fetch_array($this->result, MYSQL_ASSOC))
                    $rows[] = $row;
                break;

            case 'interbase':
                while ($row = ibase_fetch_assoc($this->result))
                    $rows[] = $row;
                break;

            default:
                $this->setError("fetchResult", "No se ha indicado el tipo de base de datos");
                break;
        }
        return $rows;
    }

    /**
     * Devuelve un array de registros
     * 
     * @param string $tp Nombre o alias de la tabla principal 
     * @param string $select La sentencia select sin el where
     * @param string $where La parte WHERE
     * @param string $orderBy La parte ORDER By
     * @param string $limit La parte LIMIT
     * @return array Array de registros obtenidos
     */
    public function getResult($tp, $select, $where = '', $orderBy = '', $limit = '') {

        // Criterio de ordenación
        $orderBy = ($orderBy != '') ? $orderBy : "{$tp}.SortOrder";

        // Limit
        $limit = ($limit != '') ? "LIMIT {$limit}" : "";

        // Condición de vigencia
        $ahora = date("Y-m-d H:i:s");
        $filtro = "({$tp}.Deleted='0') AND ({$tp}.Publish='1') AND ({$tp}.ActiveFrom<='{$ahora}') AND ( ({$tp}.ActiveTo>='{$ahora}') or ({$tp}.ActiveTo='0000-00-00 00:00:00') )";

        // Condición de privacidad
        if (!$_SESSION['usuarioWeb']['Id']) {
            $filtro .= " AND ( ({$tp}.Privacy='0') OR ({$tp}.Privacy='2') )";
        } else {
            $idPerfil = $_SESSION['usuarioWeb']['IdPerfil'];
            $filtro .= " AND ( ({$tp}.Privacy='2') OR ({$tp}.Privacy='1') OR LOCATE('{$idPerfil}',{$tp}.AccessProfileListWeb) )";
        }

        if ($where != '') {
            $filtro .= " AND {$where}";
        }

        $query = "{$select} WHERE {$filtro} ORDER BY {$orderBy} {$limit}";
        //echo $query;
        $this->query($query);

        return $this->fetchResult();
    }

    /**
     * Devuelve $limit filas encontradas a partir de la fila $offset
     * Este metodo es similar a fetchResult.
     * @param integer $limit
     * @param integer $offset
     * @return array Las filas encontradas
     */
    public function fetchResultLimit($limit, $rowNumber = '') {
        $rows = array();
        $nfilas = 0;
        if ($rowNumber < 0)
            $rowNumber = 1;

        switch (self::$dbEngine) {
            case 'mysql':
                @mysql_data_seek($this->result, $rowNumber);
                while (($row = mysql_fetch_array($this->result, MYSQL_ASSOC)) and ($nfilas < $limit)) {
                    $nfilas++;
                    $rows[] = $row;
                }
                break;

            case 'mssql':
                //NO ESTA BIEN IMPLEMENTADO
                while ($row = mssql_fetch_array($this->result, MYSQL_ASSOC))
                    $rows[] = $row;
                break;

            case 'interbase':
                //NO ESTA BIEN IMPLEMENTADO
                while ($row = ibase_fetch_assoc($this->result))
                    $rows[] = $row;
                break;
            default:
                $this->setError("fetchResultLimit", "No se ha indicado el tipo de base de datos");
                break;
        }
        return $rows;
    }

    /**
     * Devuelve el numero de columnas de la consulta
     * @return integer El numero de columnas de la consulta
     */
    public function numFields() {
        switch (self::$dbEngine) {
            case 'mysql':
                return mysql_num_fields($this->result);
                break;

            case 'mssql':
                return mssql_num_fields($this->result);
                break;

            case 'interbase':
                return ibase_num_fields($this->result);

            default:
                $this->setError("numFields", "No se ha indicado el tipo de base de datos");
                break;
        }
    }

    /**
     * Devuelve el numero de filas de la consulta
     * @return integer El numero de filas de la consulta
     */
    public function numRows() {
        switch (self::$dbEngine) {
            case 'mysql':
                return (is_resource($this->result)) ? mysql_num_rows($this->result) : 0;
                break;

            case 'mssql':
                return (is_resource($this->result)) ? mssql_num_rows($this->result) : 0;
                break;

            case 'interbase': //NO IMPLEMENTADO
                return false;
                break;

            default:
                $this->setError("numRows", "No se ha indicado el tipo de base de datos");
                break;
        }
    }

    /**
     * Se posiciona en el numero de registro indicado
     * @param integer $rowNumber El numero de registro a donde posicionarse
     * @return boolean
     */
    public function dataSeek($rowNumber) {
        switch (self::$dbEngine) {
            case 'mysql':
                return mysql_data_seek($this->result, $rowNumber);
                break;

            case 'mssql':
                //No implementado
                return false;
                break;

            case 'interbase':
                //No implementado
                return false;
                break;

            default:
                $this->setError("dataSeek", "No se ha indicado el tipo de base de datos");
                return false;
                break;
        }
    }

    /**
     * Devuelve el ID del ultimo insert
     * @return inter
     */
    public function getInsertId() {
        switch (self::$dbEngine) {
            case 'mysql':
                //return mysql_insert_id(self::$dbLinkInstance);
                $result = mysql_query("SELECT LAST_INSERT_ID()",self::$dbLinkInstance);
                $row = mysql_fetch_row($result);
                return $row[0];
                break;

            case 'mssql':
                //No implementado
                return 0;
                break;

            case 'interbase':
                //No implementado
                return 0;
                break;

            default:
                $this->setError("getInsertId", "No se ha indicado el tipo de base de datos");
                return 0;
                break;
        }
    }

    /**
     * Devuelve el numero de filas afectadas en la ultima consulta
     * @return integer
     */
    public function getAffectedRows() {
        return $this->affectedRows;
    }

    /**
     * Devuelve el nombre del servidor de datos
     * @return string El nombre del servidor
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Devuelve el usuario de conexion a la base de datos
     * @return string Usuario
     */
    public function getUser() {
        return self::$user;
    }

    /**
     * Devuelve el password de conexion a la base de datos
     * @return string Password
     */
    public function getPassword() {
        return self::$password;
    }

    /**
     * Devuelve el nombre de la base de datos
     * @return string Nombre de la base de datos
     */
    public function getDataBase() {
        return self::$dataBase;
    }

    public function getDbLink() {
        return self::$dbLinkInstance;
    }

    /**
     * Genera el mensaje de error haciendo una gestión individualizada
     * de algunos errores dependiendo del código de error y del motor de base de datos
     *
     * @param string $method El nombre del método que capturó el error
     * @param string $error Mensaje de error personalizado (opcional)
     */
    public function setError($method, $error = '') {

        $mensaje = "EntityManager [{$method}]: ";

        if ($error != '')
            $mensaje .= $error;
        else {
            switch (self::$dbEngine) {
                case 'mysql':
                    switch (mysql_errno()) {
                        case '1062':
                            $mensaje .= "Datos duplicados. " . mysql_error();
                            break;
                        default:
                            $mensaje .= mysql_errno() . " " . mysql_error();
                            break;
                    }
                    break;

                default:
                    $mensaje .= mysql_errno() . " " . mysql_error();
                    break;
            }
        }

        $this->error[] = $mensaje;
    }

    /**
     * Devuelve un array con los errores producidos
     * @return array Eventuales errores
     */
    public function getError() {
        return $this->error;
    }

}

?>
