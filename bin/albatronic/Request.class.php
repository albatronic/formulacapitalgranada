<?php

/**
 * CLASE REQUEST.
 *
 * Almacena y devuelve diferentes variables
 * descriptivas de la petición request.
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC, SL
 * @version 1.0 22.05.2011
 */
class Request {

    /**
     * Parámetros que vienen por GET
     * @var array
     */
    private $parameters;

    /**
     * Método empleado en la peticion:
     * GET, POST, COOKIE
     * @var string
     */
    private $method;

    /**
     * Parámetros que viene por POST
     * @var array
     */
    private $request;

    /**
     * Lenguaje aceptado
     * @var string
     */
    private $acceptLanguage;

    /**
     * Direccion IP del visitante
     * @var string
     */
    private $remoteAddr;

    /**
     * Url desde donde viene el visitante
     * @var string
     */
    private $httpReferer;

    /**
     * Navegador
     * @var string
     */
    private $userAgent;

    /**
     * Tipo de contenido
     * @var string
     */
    private $contentType;

    /**
     * Array con los nombre de los navegadores obsoletos
     * @var array
     */
    private $oldBrowsers = array(
        'MSIE 8',
        'MSIE 7',
        'MSIE 6',
    );

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->request = $_REQUEST;
        $this->request['FILES'] = $_FILES;
        $this->acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->remoteAddr = $this->getIpReal();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->contentType = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : "";
        $this->httpReferer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "";
    }

    /**
     * Analiza la Url amigable, la trozea y almacena
     * sus componentes en el array $parametros
     * @return array Parametros de la url
     */
    private function getUrl() {
        $parameters = array();
        $url = parse_url($_SERVER['REQUEST_URI']);
        foreach (explode("/", $url['path']) as $p)
            if ($p != '')
                $parameters[] = $p;

        return $parameters;
    }

    /**
     * Devuelve la url amigable
     *
     * @param string $appPath El path de la web
     * @return string La url amigable sin el dominio, sin el path de la web
     */
    public function getUrlFriendly($appPath) {
        // Cojo la url, incluido el path a la aplicacion
        switch ($this->method) {
            case 'GET' :
                $url = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
                // A la url le quito la parte del path a la aplicacion
                $urlSinPath = explode("/", str_replace($appPath, "", $url));
                $urlAmigable = "/" . $urlSinPath[1];
                break;
            case 'POST':
                $url = $this->request['controller'];
                // A la url le quito la parte del path a la aplicacion
                $urlSinPath = explode("/", str_replace($appPath, "", $url));
                $urlAmigable = "/" . $urlSinPath[0];
                break;
        }

        return $urlAmigable;
    }

    /**
     * Devuelve un array con los valores enviados por GET de la url amigable.
     *
     * IMPORTANTE:
     *   No se incluye en el array el path de la aplicación, solo lo parámetros.
     *
     *   Ej: en el caso http://www.demo.com/app/Erp/Clientes/edit/1
     *
     *   Si el path de la aplicacion es "app/Erp", los parametros que se
     *   devuelven son:
     *   array('Clientes','edit','1')
     *
     * @param string $appPath path de la aplicacion
     * @return array Array con los parametros de la url
     */
    public function getParameters($appPath) {

        $this->parameters = array();

        // Cojo la url, incluido el path a la aplicacion
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // A la url le quito la parte del path a la aplicacion
        $params = str_replace($appPath, "", $url);
        // Troceo los parámetros y los meto en un array.
        // El primer parámetro tendrá el índice 0 en el array
        foreach (explode("/", $params) as $p)
            if ($p != '')
                $this->parameters[] = $p;

        return $this->parameters;
    }

    /**
     * Devuelve un string con el método (GET,POST,COOKIE)
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Devuelve un array con las variables enviadas por POST
     * @return array
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Devuelve un string con el idioma aceptado por el cliente (ej: es-ES)
     * @return string
     */
    public function getLanguage() {
        return $this->acceptLanguage;
    }

    /**
     * Devuelve un string con el idiona aceptado por el cliente con 2 caracteres
     * @return string El lenguaje expresado en 2 caracteres
     */
    public function getShortLanguage() {
        return substr($this->acceptLanguage, 0, 2);
    }

    /**
     * Devuelve un string con la dirección IP del cliente
     * @return string
     */
    public function getRemoteAddr() {
        return $this->remoteAddr;
    }

    /**
     * Devuelve un string con la url desde la que procede el visitante
     * @return string
     */
    public function getHttpReferer() {
        return $this->httpReferer;
    }

    /**
     * Devuelve un string con el navegador utilizado por el cliente
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Devuelve un string con el tipo de contenido de la pagina
     * @return string
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * Devuelve true si el navegador es antiguo
     *
     *   Se consideran browsers antiguos los definidos en el array $this->oldBrowsers,
     *   actualmente son:
     *
     *   - MSIE 6
     *   - MSIE 7
     *
     * @return boolean
     */
    public function isOldBrowser() {

        $isOld = false;

        foreach ($this->oldBrowsers as $value) {
            $isOld = ( strpos($this->getUserAgent(), $value) != 0 );
            if ($isOld)
                break;
        }

        return $isOld;
    }

    /**
     * Devuelve TRUE / FALSE dependiendo si estás en entorno
     * de desarrollo o de producción respectivamente.
     *
     * @return boolean TRUE si estás en entorno desarrollo
     */
    public function isDevelopment() {
        return ( ($_SERVER['SERVER_NAME'] == 'localhost') or (substr($_SERVER['SERVER_NAME'], 0, 3) != 'www') );
    }

    /**
     * Obtiene la ip real del visitante teniendo en cuenta
     * que puede venir a través de un proxy.
     * 
     * @return string La ip
     */
    public function getIpReal() {

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $client_ip =
                    (!empty($_SERVER['REMOTE_ADDR']) ) ?
                    $_SERVER['REMOTE_ADDR'] :
                    ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                            $_ENV['REMOTE_ADDR'] :
                            "unknown" );

            // los proxys van añadiendo al final de esta cabecera
            // las direcciones ip que van "ocultando". Para localizar la ip real
            // del usuario se comienza a mirar por el principio hasta encontrar 
            // una dirección ip que no sea del rango privado. En caso de no 
            // encontrarse ninguna se toma como valor el REMOTE_ADDR

            $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);

            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $private_ip = array(
                        '/^0\./',
                        '/^127\.0\.0\.1/',
                        '/^192\.168\..*/',
                        '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                        '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip =
                    (!empty($_SERVER['REMOTE_ADDR']) ) ?
                    $_SERVER['REMOTE_ADDR'] :
                    ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                            $_ENV['REMOTE_ADDR'] :
                            "unknown" );
        }

        return $client_ip;
    }

}

?>
