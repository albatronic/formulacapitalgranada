<?php

/**
 * CONTROLADOR FRONTAL. Recibe todas las peticiones y renderiza el resultado
 *
 * UTILIZA URLS AMIGABLES. DEBE EXISTIR UN '.htaccess' EN EL DIRECTORIO
 * RAIZ DEL SITIO WEB CON UNA REGLA QUE DERIVA TODAS LAS PETICIONES
 * A ESTE SCRIPT (CONTROLADOR FRONTAL)
 *
 * EJEMPLO .htaccess:
 * <IfModule mod_rewrite.c>
 *   RewriteEngine On
 *   RewriteCond %{REQUEST_FILENAME} !-f
 *   RewriteRule ^(.*)$ index.php [QSA,L]
 * </IfModule>
 *
 * MAS INFO: http://httpd.apache.org/docs/2.0/es/
 *
 * LAS PETICIONES DEBEN SER EN EL FORMATO:
 * http://www.sitioweb.com/apppath/urlAmigable/resto de valores...
 *
 * El apppath puede estar compuesto de varios subcarpetas. Ej:
 * http://www.sitioweb.com/apps/gestion/urlAmigable/ resto de valores...
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 27.05.2011
 */
session_start();

// error_reporting(E_ALL);
$_SESSION['IdSesion'] = session_id();

if (!isset($_SESSION['usuarioWeb']['Id'])) {
    $_SESSION['usuarioWeb']['Id'] = 0;
}

if (file_exists("bin/yaml/lib/sfYaml.php")) {
    include "bin/yaml/lib/sfYaml.php";
} else {
    die("NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML");
}

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
if (!isset($_SESSION['configWeb'])) {
    if (!file_exists('config/config.yml')) {
        die("NO EXISTE EL FICHERO DE CONFIGURACION");
    } else {
        $yaml = sfYaml::load('config/config.yml');
        $_SESSION['configWeb'] = $yaml['config'];
    }
}

$config = $_SESSION['configWeb'];

$_SESSION['project']['ftpWeb'] = $config['projectFtp'];

$app = $config['app'];

$_SESSION['appPath'] = $app['path'];
$_SESSION['appUrl'] = $app['url'];
$_SESSION['theme'] = $app['theme'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define("APP_PATH", $_SERVER['DOCUMENT_ROOT'] . $app['path'] . "/" . $app['theme']);
include_once $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . '/cache/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
    $app['framework'],
    'entities/',
    'lib/',
));
spl_autoload_register(array('Autoloader', 'loadClass'));

//----------------------------------------------------------------
// ACTIVAR EL MOTOR TWIG PARA LOS TEMPLATES.
//----------------------------------------------------------------
if (file_exists($config['twig']['motor'])) {
    include_once $config['twig']['motor'];
    Twig_Autoloader::register();

    $cache = "{$app['theme']}/cache";
    if ($cache != '')
        $ops['cache'] = $cache;
    $debug = $config['twig']['debug_mode'];
    if ($debug != '')
        $ops['debug'] = $debug;
    $charset = $config['twig']['charset'];
    if ($charset != '')
        $ops['charset'] = $charset;
    $ops['autoescape'] = true;
    $loader = new Twig_Loader_Filesystem($app['theme'] . "/modules");
    $twig = new Twig_Environment($loader, $ops);
    $twig->getExtension('core')->setNumberFormat(2, ',', '.');
} else
    die("NO SE PUEDE ENCONTRAR EL MOTOR TWIG");

// ------------------------------------------------
// COMPROBAR DISPOSITIVO DE NAVEGACION
// ------------------------------------------------
if (!isset($_SESSION['isMobile'])) {
    $browser = new Browser();
    $_SESSION['isMobile'] = $browser->isMobile();
    unset($browser);
}

// ----------------------------------------------------------------
// CARGAR LO QUE VIENE EN EL REQUEST
// ----------------------------------------------------------------
$rq = new Request();

// ----------------------------------------------------------------
// DETERMINAR ENTORNO DE DESARROLLO O DE PRODUCCION
// ----------------------------------------------------------------
$_SESSION['EntornoDesarrollo'] = $rq->isDevelopment();

// ----------------------------------------------------------------
// EN ENTORNO DE PRODUCCION, OBTENER EL ORIGEN DE LA PETICION PARA
// LA GESTION DE VISITAS
// ----------------------------------------------------------------
if ((!$_SESSION['EntornoDesarrollo']) and (!$_SESSION['origen'])) {
    //$_SESSION['origen'] = WebService::getOrigenVisitante($config['wsControlVisitas'] . $rq->getRemoteAddr());
}
// ----------------------------------------------------------------
// ACTIVAR EL FORMATO DE LA MONEDA
// ----------------------------------------------------------------
setlocale(LC_MONETARY, $rq->getLanguage());
//if ((!isset($_SESSION['idiomas']['actual'])) or ($_SESSION['EntornoDesarrollo']))
if (!isset($_SESSION['idiomas']['actual'])) {
    ControllerWeb::setIdioma();
}

// Si el navegador es antiguo muestro template especial
$url = new CpanUrlAmigables();
if ($rq->isOldBrowser()) {
    $rows = $url->cargaCondicion("Id,Idioma,UrlFriendly,Controller,Action,Parameters,Entity,IdEntity", "UrlFriendly='/oldbrowser'");
} else {
    // Localizar la url amigable
    $rows = $url->cargaCondicion("Id,Idioma,UrlFriendly,Controller,Action,Parameters,Entity,IdEntity", "Idioma='{$_SESSION['idiomas']['actual']}' and UrlFriendly='{$rq->getUrlFriendly($app['path'])}'"); 
// Localizar la url amigable
    //$rows[0] = $url->matchUrl($rq->getUrlFriendly($app['path']));

    if (count($url->getErrores()) == 0) {
        if (!$rows[0]['Id']) {
            $rows = $url->cargaCondicion("Id,Idioma,UrlFriendly,Controller,Action,Parameters,Entity,IdEntity", "UrlFriendly='/error404'");
        }
    } else {
        print_r($url->getErrores());
        die("Error de conexión a la BD");
    }
}

unset($url);
$row = $rows[0];
if (!$row['Id']) die("No se ha localizado ninguna url amigable");

$_SESSION['idiomas']['actual'] = $row['Idioma'];
$_SESSION['urlFriendly'] = $row['UrlFriendly'];

//-----------------------------------------------------------------
// INSTANCIAR UN OBJETO DE LA CLASE REQUEST PARA TENER DISPONIBLES
// TODOS LOS VALORES QUE CONSTITUYEN LA PETICION E IDENTIFICAR
// SI LA PETICION ES 'GET' O 'POST', ASI COMO EL CONTROLADOR Y
// ACCION SOLICITADA.
//-----------------------------------------------------------------
switch ($rq->getMethod()) {
    case 'GET':
        $request = $rq->getParameters($app['path']);
        $request['METHOD'] = "GET";
        //$controller = ucfirst($request[0]);
        //$action = $request[1];
        $controller = ucfirst($row['Controller']);
        $action = $row['Action'];
        $request['IdUrlAmigable'] = $row['Id'];
        $request['Parameters'] = $row['Parameters'];
        $request['Entity'] = $row['Entity'];
        $request['IdEntity'] = $row['IdEntity'];
        break;

    case 'POST':
        $request = $rq->getRequest();
        $request['METHOD'] = "POST";
        $controller = ucfirst($request['controller']);
        $action = $request['action'];
        $request['IdUrlAmigable'] = $row['Id'];
        $request['Parameters'] = $row['Parameters'];
        $request['Entity'] = $row['Entity'];
        $request['IdEntity'] = $row['IdEntity'];
        break;
}

// Si no se ha localizado el controlador, lo pongo a Error404
if ($controller == '') {
    $controller = 'Error404';
}
// Si no se ha localizado la action, la pongo a Index
if ($action == '') {
    $action = "Index";
}

// Si no existe el controller lo pongo a 'Error404'
$fileController = "{$_SESSION['theme']}/modules/{$controller}/{$controller}Controller.class.php";

if (!file_exists($fileController)) {
    $controller = "Error404";
    $fileController = "{$_SESSION['theme']}/modules/Error404/Error404Controller.class.php";
}

$clase = $controller . "Controller";
$metodo = $action . "Action";

//------------------------------------------------------------------------------
// INSTANCIAR EL CONTROLLER REQUERIDO
// SI EL METODO SOLICITADO EXISTE, LO EJECUTO, SI NO EJECUTO EL METODO INDEX
// RENDERIZAR EL RESULTADO CON EL TEMPLATE Y DATOS DEVUELTOS
// SI NO EXISTE EL TEMPLATE DEVUELTO, MUESTRO UNA PAGINA DE ERROR
//------------------------------------------------------------------------------
include_once $fileController;
$con = new $clase($request);
if (!method_exists($con, $metodo)) {
    $metodo = "IndexAction";
}
$result = $con->{$metodo}();

// Si el navegador es mobile y existe la template mobile, lo muestro.
// En caso contrario muestro el template normal.
if ($_SESSION['isMobile']) {
    $aux = str_replace('.html.twig', '.mobile.html.twig', $result['template']);
    if (file_exists("modules/{$aux}")) {
        $result['template'] = $aux;
    }
}

$result['values']['urlAmigable'] = $_SESSION['urlFriendly'];
$result['values']['controller'] = $controller;
$result['values']['action'] = $metodo;
$result['values']['archivoCss'] = ControllerWeb::getArchivoCss($result['template']);
$result['values']['archivoJs'] = ControllerWeb::getArchivoJs($result['template']);

// Cargo los valores para el modo debuger
if ($config['debug_mode']) {
    $result['values']['_debugMode'] = true;
    $result['values']['_auditMode'] = (string) $config['audit_mode'];
    $result['values']['_user'] = sfYaml::dump($_SESSION['usuarioWeb'], 5);
    $result['values']['_debugValues'] = sfYaml::Dump($result['values'], 100);
}

// Si el método no devuelve template o no exite, muestro un template de error.
if (!file_exists($app['theme'] . '/modules/' . $result['template']) or ($result['template'] == '')) {
    $result['values']['error'] = 'No existe el template: "' . $result['template'] . '" devuelto por el método "' . $clase . ':' . $action . 'Action"';
    $result['template'] = ($_SESSION['isMobile']) ?
            '_global/error.mobile.html.twig' :
            '_global/error.html.twig';
}

// Establecer el layout segun el dispositivo de navegación
// Si fuese mobile pero no hay layout mobile, se toma el layout laptop
if (!isset($_SESSION['layout'])) {
    $_SESSION['layout'] = "_global/layoutLaptop.html.twig";
    if ($_SESSION['isMobile']) {
        $aux = "_global/layoutMobile.html.twig";
        if (file_exists($app['theme'] . "/modules/" . $aux)) {
            $_SESSION['layout'] = $aux;
        }
    }
}

// Renderizo el template y los valores devueltos por el método
$twig->addGlobal('appPath', $app['path']);
$twig->addGlobal('theme', $app['theme']);
$twig->addGlobal('language', $_SESSION['LANGUAGE']);
$twig->addGlobal('urlAmigable', $row['UrlFriendly']);
$twig->addGlobal('LABELS', $result['values']['LABELS']);
unset($result['values']['LABELS']);
$twig->loadTemplate($result['template'])
        ->display(array(
            'layout' => $_SESSION['layout'],
            'values' => $result['values'],
            'app' => $app,
            'chequeadaResolucionVisitante' => isset($_SESSION['resolucionVisitante']),
            'user' => $_SESSION['usuarioWeb'],
        ));

//------------------------------------------------------------

unset($rq);
unset($con);
unset($loader);
unset($twig);
unset($config);
