<?php

/**
 * CREAR EL SITEMAP DEL SITIO EN BASE A LAS SECCIONES
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 08.08.2014
 */
session_start();

if (!file_exists('config/config.yml')) {
    echo "NO EXISTE EL FICHERO DE CONFIGURACION";
    exit;
}

if (file_exists("bin/yaml/lib/sfYaml.php")) {
    include "bin/yaml/lib/sfYaml.php";
} else {
    echo "NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML";
    exit;
}

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
$config = sfYaml::load('config/config.yml');
$app = $config['config']['app'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define(APP_PATH, $_SERVER['DOCUMENT_ROOT'] . $app['path'] . "/");
include_once  $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . 'tmp/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
            $app['framework'],
            'entities/',
            'lib/',
        ));
spl_autoload_register(array('Autoloader', 'loadClass'));

$diaActual = date("d");
$mesActual = date("m");
$anoActual = date("Y");
$horaAzar = mt_rand(1, 9); //date("H:i:s");
$fechaAnterior = date("Y-m-d", mktime(0, 0, 0, $mesActual, $diaActual-1, $anoActual) );
$lastMod = $fechaAnterior .'T0'.$horaAzar.':'.rand(10, 59).':'.rand(10, 59).'+00:00';

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
  xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';


$seccion = new GconSecciones();
$rows = $seccion->cargaCondicion("UrlFriendly,DATE_FORMAT(ModifiedAt,'%Y-%m-%d') ModifiedAt,ChangeFreqSitemap,ImportanceSitemap");
unset($seccion);

$urls = "";
foreach ($rows as $row) {
    $urls .= "<url> 
    <loc>{$app['url']}{$row['UrlFriendly']}</loc>
    <lastmod>{$row['ModifiedAt']}</lastmod>
    <changefreq>{$row['ChangeFreqSitemap']}</changefreq>
    <priority>{$row['ImportanceSitemap']}</priority>
  </url>";
}

$xml = $xml . $urls . "</urlset>";


header("Content-Type: application/xml; charset=utf-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ". gmdate("D, d M Y H:i:s") ." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $xml;
