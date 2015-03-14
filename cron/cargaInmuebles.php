<?php

/*
 * PROCESO DE CARGA DE INMUEBLES DESDE EL FEED XML
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 12.03.2015
 */

// Notificar solamente errores de ejecución
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();

if (!file_exists('../config/config.yml')) {
    echo "NO EXISTE EL FICHERO DE CONFIGURACION";
    exit;
}

if (file_exists("../bin/yaml/lib/sfYaml.php")) {
    include "../bin/yaml/lib/sfYaml.php";
} else {
    echo "NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML";
    exit;
}

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
$config = sfYaml::load('../config/config.yml');
$app = $config['config']['app'];
$_SESSION['conections'] = $config['config']['conections'];
$_SESSION['produccion'] = !$config['config']['debug_mode'];
$_SESSION['appPath'] = $app['path'];
$_SESSION['idiomas']['actual'] = 0;
$_SESSION['usuarioPortal']['Id'] = 1;
$_SESSION['usuarioWeb']['Id'] = 1;
$_SESSION['project']['conection'] = 'pcae';
$theme = $config['config']['app']['theme'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define("APP_PATH", str_replace("cron", "", __DIR__));
include_once "../" . $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . "{$theme}/cache/class_path_cache.txt");
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
    '../' . $app['framework'],
    '../entities/',
    '../lib/',
));
spl_autoload_register(array('Autoloader', 'loadClass'));

class CargaInmuebles {

    static $fpLog = null;
    static $feed = "../../feed/inmuebles_22506_1.xml";
    static $log = "../log/cargaInmuebles.log";

    static function leeFeed() {
        if (file_exists(self::$feed)) {
            $xmlRead = new XmlRead(self::$feed);
            $xml = $xmlRead->getXml();
        } else {
            $xml = "";
        }
        return $xml;
    }

    static function log($message) {

        if (self::$fpLog == NULL) {
            $fp = fopen(self::$log, "a");
        }
        if ($fp) {
            fwrite($fp, date('Y-m-d H:i:s') . "\tFEED DE INMUEBLES\t{$message}\n");
        }
    }

    static function limpia($texto) {
        $texto = str_replace("[CDATA]", "", $texto);
        $texto = str_replace("[/CDATA]", "", $texto);
        $texto = base64_decode($texto);
        return $texto;
    }

}

$inmuebles = CargaInmuebles::leeFeed();

if ($inmuebles != '') {
    $schema = array();

    $inmo = new Inmuebles();
    $inmo->truncate();
    
    CargaInmuebles::log("Se vacia la tabla de inmuebles");
    
    $nOK = $nKO = 0;
    
    foreach ($inmuebles->Inmuebles->Inmueble as $inmueble) {
        foreach ($inmueble as $key => $value) {
            if (strpos($value, "[CDATA]") !== FALSE) {
                $value = CargaInmuebles::limpia($value);
            }
            $data[$key] = $value;
        }
        unset($data['Fotos']);
        unset($data['Idiomas']);

        $inmo = new Inmuebles();
        $inmo->bind($data);
        ($inmo->create() > 0) ? $nOK ++ : $nKO ++;        
    }
    CargaInmuebles::log("Inmuebles OK {$nOK}");
    CargaInmuebles::log("Inmuebles KO {$nKO}");
    
} else {
    CargaInmuebles::log("El archivo " . CargaInmuebles::$feed . " no existe o está vacio");
}
