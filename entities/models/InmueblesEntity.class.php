<?php

/**
 * @copyright GRUPO TREVENQUE
 * @date 12.03.2015 23:39:22
 */

/**
 * @orm:Entity(Inmuebles)
 */
class InmueblesEntity extends EntityComunes {

    /**
     * @orm GeneratedValue
     * @orm Id
     * @var integer
     * @assert NotBlank(groups="Inmuebles")
     */
    protected $Id;

    /**
     * @var integer
     */
    protected $count = '0';

    /**
     * @var integer
     */
    protected $cod_elemento = '0';

    /**
     * @var string
     */
    protected $cod_oficina;

    /**
     * @var string
     */
    protected $Cod_Provincia;

    /**
     * @var string
     */
    protected $Cod_Poblacion;

    /**
     * @var string
     */
    protected $Cod_Zona;

    /**
     * @var string
     */
    protected $cod_tipoelem;

    /**
     * @var string
     */
    protected $des_tipoelem;

    /**
     * @var string
     */
    protected $cod_destino;

    /**
     * @var string
     */
    protected $Num_Referencia;

    /**
     * @var string
     */
    protected $codigo_interno;

    /**
     * @var integer
     */
    protected $m2utiles = '0';

    /**
     * @var integer
     */
    protected $m2constr = '0';

    /**
     * @var integer
     */
    protected $m2parc = '0';

    /**
     * @var integer
     */
    protected $m2terraza = '0';

    /**
     * @var integer
     */
    protected $precio;

    /**
     * @var integer
     */
    protected $precio_anterior;

    /**
     * @var string
     */
    protected $comentarios;

    /**
     * @var string
     */
    protected $anioantiguedad;

    /**
     * @var integer
     */
    protected $numhabitac = '0';

    /**
     * @var integer
     */
    protected $numbanios = '0';

    /**
     * @var integer
     */
    protected $numaseos = '0';

    /**
     * @var string
     */
    protected $cod_tipsue;

    /**
     * @var integer
     */
    protected $val_cuomen = '0';

    /**
     * @var integer
     */
    protected $gastos_comunidad = '0';

    /**
     * @var integer
     */
    protected $comunidad_incluida = '0';

    /**
     * @var string
     */
    protected $rutagraf;

    /**
     * @var string
     */
    protected $fechaing;

    /**
     * @var string
     */
    protected $Estado;

    /**
     * @var string
     */
    protected $est_vaca;

    /**
     * @var string
     */
    protected $des_provincia;

    /**
     * @var string
     */
    protected $des_poblacion;

    /**
     * @var string
     */
    protected $des_zona;

    /**
     * @var string
     */
    protected $carpeta;

    /**
     * @var integer
     */
    protected $num_salones = '0';

    /**
     * @var integer
     */
    protected $num_armarios = '0';

    /**
     * @var integer
     */
    protected $num_terrazas = '0';

    /**
     * @var integer
     */
    protected $num_pgaraje = '0';

    /**
     * @var integer
     */
    protected $num_paparcam = '0';

    /**
     * @var string
     */
    protected $des_orientacion;

    /**
     * @var string
     */
    protected $des_vista;

    /**
     * @var string
     */
    protected $des_coficial;

    /**
     * @var string
     */
    protected $situacion;

    /**
     * @var tinyint
     */
    protected $est_estrenar = '0';

    /**
     * @var string
     */
    protected $estgen;

    /**
     * @var string
     */
    protected $ubicacion;

    /**
     * @var integer
     */
    protected $nlocales = '0';

    /**
     * @var integer
     */
    protected $msup_patio = '0';

    /**
     * @var integer
     */
    protected $pax = '0';

    /**
     * @var integer
     */
    protected $ntrasteros = '0';

    /**
     * @var integer
     */
    protected $nascensor = '0';

    /**
     * @var string
     */
    protected $techo;

    /**
     * @var string
     */
    protected $ventana;

    /**
     * @var integer
     */
    protected $mt2largofachada = '0';

    /**
     * @var string
     */
    protected $climatizacion;

    /**
     * @var string
     */
    protected $calefaccion;

    /**
     * @var string
     */
    protected $cocina;

    /**
     * @var string
     */
    protected $aguacaliente;

    /**
     * @var string
     */
    protected $amueblado;

    /**
     * @var integer
     */
    protected $ndespachos = '0';

    /**
     * @var string
     */
    protected $zonascomunes;

    /**
     * @var string
     */
    protected $est_destacado;

    /**
     * @var tinyint
     */
    protected $est_oferta;

    /**
     * @var tinyint
     */
    protected $est_escaparate;

    /**
     * @var tinyint
     */
    protected $est_cartel;

    /**
     * @var integer
     */
    protected $cod_cliente = '0';

    /**
     * @var string
     */
    protected $url_banco;

    /**
     * @var integer
     */
    protected $nbalcones = '0';

    /**
     * @var integer
     */
    protected $npisosedif = '0';

    /**
     * @var integer
     */
    protected $npisosbloque = '0';

    /**
     * @var string
     */
    protected $num_planta;

    /**
     * @var string
     */
    protected $r1;

    /**
     * @var string
     */
    protected $r2;

    /**
     * @var string
     */
    protected $cod_postal;

    /**
     * @var string
     */
    protected $certificado_energetico;

    /**
     * @var string
     */
    protected $Direccion;

    /**
     * @var string
     */
    protected $grupos;

    /**
     * @var tinyint
     */
    protected $enviar_direccion = '0';

    /**
     * @var string
     */
    protected $suma_numeros;

    /**
     * @var integer
     */
    protected $tipo_direccion = '0';

    /**
     * @var string
     */
    protected $escaparate;

    /**
     * @var tinyint
     */
    protected $tipo_vacacional = '0';

    /**
     * @var tinyint
     */
    protected $reserva_vacacional = '0';

    /**
     * @var string
     */
    protected $observacionRuso;

    /**
     * @var string
     */
    protected $datos;

    /**
     * @var string
     */
    protected $des_cercanias;

    /**
     * @var string
     */
    protected $financiacion;

    /**
     * @var integer
     */
    protected $cod_categoria = '0';

    /**
     * @var string
     */
    protected $google_map;

    /**
     * @var string
     */
    protected $des_alquiler;

    /**
     * @var string
     */
    protected $mandato;

    /**
     * @var string
     */
    protected $mostrar_coordenadas;

    /**
     * @var string
     */
    protected $coordenadas;

    /**
     * @var integer
     */
    protected $zoom_coordenadas = '0';

    /**
     * @var tinyint
     */
    protected $solo_web = '0';

    /**
     * @var string
     */
    protected $estadoinmueble;

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = '';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'Inmuebles';

    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

    /**
     * Array con las columnas de la primarykey
     * @var array
     */
    protected $_arrayPrimaryKeys = array('Id');

    /**
     * Relacion de entidades que dependen de esta
     * @var string
     */
    protected $_parentEntities = array(
    );

    /**
     * Relacion de entidades de las que esta depende
     * @var string
     */
    protected $_childEntities = array(
        'ValoresSN',
        'ValoresPrivacy',
        'ValoresDchaIzq',
        'ValoresChangeFreq',
        'RequestMethods',
        'RequestOrigins',
        'CpanAplicaciones',
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setId($Id) {
        $this->Id = $Id;
    }

    public function getId() {
        return $this->Id;
    }

    public function setcount($count) {
        $this->count = $count;
    }

    public function getcount() {
        return $this->count;
    }

    public function setcod_elemento($cod_elemento) {
        $this->cod_elemento = $cod_elemento;
    }

    public function getcod_elemento() {
        return $this->cod_elemento;
    }

    public function setcod_oficina($cod_oficina) {
        $this->cod_oficina = trim($cod_oficina);
    }

    public function getcod_oficina() {
        return $this->cod_oficina;
    }

    public function setCod_Provincia($Cod_Provincia) {
        $this->Cod_Provincia = trim($Cod_Provincia);
    }

    public function getCod_Provincia() {
        return $this->Cod_Provincia;
    }

    public function setCod_Poblacion($Cod_Poblacion) {
        $this->Cod_Poblacion = trim($Cod_Poblacion);
    }

    public function getCod_Poblacion() {
        return $this->Cod_Poblacion;
    }

    public function setCod_Zona($Cod_Zona) {
        $this->Cod_Zona = trim($Cod_Zona);
    }

    public function getCod_Zona() {
        return $this->Cod_Zona;
    }

    public function setcod_tipoelem($cod_tipoelem) {
        $this->cod_tipoelem = trim($cod_tipoelem);
    }

    public function getcod_tipoelem() {
        return $this->cod_tipoelem;
    }

    public function setdes_tipoelem($des_tipoelem) {
        $this->des_tipoelem = trim($des_tipoelem);
    }

    public function getdes_tipoelem() {
        return $this->des_tipoelem;
    }

    public function setcod_destino($cod_destino) {
        $this->cod_destino = trim($cod_destino);
    }

    public function getcod_destino() {
        return $this->cod_destino;
    }

    public function setNum_Referencia($Num_Referencia) {
        $this->Num_Referencia = trim($Num_Referencia);
    }

    public function getNum_Referencia() {
        return $this->Num_Referencia;
    }

    public function setcodigo_interno($codigo_interno) {
        $this->codigo_interno = trim($codigo_interno);
    }

    public function getcodigo_interno() {
        return $this->codigo_interno;
    }

    public function setm2utiles($m2utiles) {
        $this->m2utiles = $m2utiles;
    }

    public function getm2utiles() {
        return $this->m2utiles;
    }

    public function setm2constr($m2constr) {
        $this->m2constr = $m2constr;
    }

    public function getm2constr() {
        return $this->m2constr;
    }

    public function setm2parc($m2parc) {
        $this->m2parc = $m2parc;
    }

    public function getm2parc() {
        return $this->m2parc;
    }

    public function setm2terraza($m2terraza) {
        $this->m2terraza = $m2terraza;
    }

    public function getm2terraza() {
        return $this->m2terraza;
    }

    public function setprecio($precio) {
        $this->precio = $precio;
    }

    public function getprecio() {
        return $this->precio;
    }

    public function setprecio_anterior($precio_anterior) {
        $this->precio_anterior = $precio_anterior;
    }

    public function getprecio_anterior() {
        return $this->precio_anterior;
    }

    public function setcomentarios($comentarios) {
        $this->comentarios = trim($comentarios);
    }

    public function getcomentarios() {
        return $this->comentarios;
    }

    public function setanioantiguedad($anioantiguedad) {
        $this->anioantiguedad = trim($anioantiguedad);
    }

    public function getanioantiguedad() {
        return $this->anioantiguedad;
    }

    public function setnumhabitac($numhabitac) {
        $this->numhabitac = $numhabitac;
    }

    public function getnumhabitac() {
        return $this->numhabitac;
    }

    public function setnumbanios($numbanios) {
        $this->numbanios = $numbanios;
    }

    public function getnumbanios() {
        return $this->numbanios;
    }

    public function setnumaseos($numaseos) {
        $this->numaseos = $numaseos;
    }

    public function getnumaseos() {
        return $this->numaseos;
    }

    public function setcod_tipsue($cod_tipsue) {
        $this->cod_tipsue = trim($cod_tipsue);
    }

    public function getcod_tipsue() {
        return $this->cod_tipsue;
    }

    public function setval_cuomen($val_cuomen) {
        $this->val_cuomen = $val_cuomen;
    }

    public function getval_cuomen() {
        return $this->val_cuomen;
    }

    public function setgastos_comunidad($gastos_comunidad) {
        $this->gastos_comunidad = $gastos_comunidad;
    }

    public function getgastos_comunidad() {
        return $this->gastos_comunidad;
    }

    public function setcomunidad_incluida($comunidad_incluida) {
        $this->comunidad_incluida = $comunidad_incluida;
    }

    public function getcomunidad_incluida() {
        return $this->comunidad_incluida;
    }

    public function setrutagraf($rutagraf) {
        $this->rutagraf = trim($rutagraf);
    }

    public function getrutagraf() {
        return $this->rutagraf;
    }

    public function setfechaing($fechaing) {
        $this->fechaing = trim($fechaing);
    }

    public function getfechaing() {
        return $this->fechaing;
    }

    public function setEstado($Estado) {
        $this->Estado = trim($Estado);
    }

    public function getEstado() {
        return $this->Estado;
    }

    public function setest_vaca($est_vaca) {
        $this->est_vaca = trim($est_vaca);
    }

    public function getest_vaca() {
        return $this->est_vaca;
    }

    public function setdes_provincia($des_provincia) {
        $this->des_provincia = trim($des_provincia);
    }

    public function getdes_provincia() {
        return $this->des_provincia;
    }

    public function setdes_poblacion($des_poblacion) {
        $this->des_poblacion = trim($des_poblacion);
    }

    public function getdes_poblacion() {
        return $this->des_poblacion;
    }

    public function setdes_zona($des_zona) {
        $this->des_zona = trim($des_zona);
    }

    public function getdes_zona() {
        return $this->des_zona;
    }

    public function setcarpeta($carpeta) {
        $this->carpeta = trim($carpeta);
    }

    public function getcarpeta() {
        return $this->carpeta;
    }

    public function setnum_salones($num_salones) {
        $this->num_salones = $num_salones;
    }

    public function getnum_salones() {
        return $this->num_salones;
    }

    public function setnum_armarios($num_armarios) {
        $this->num_armarios = $num_armarios;
    }

    public function getnum_armarios() {
        return $this->num_armarios;
    }

    public function setnum_terrazas($num_terrazas) {
        $this->num_terrazas = $num_terrazas;
    }

    public function getnum_terrazas() {
        return $this->num_terrazas;
    }

    public function setnum_pgaraje($num_pgaraje) {
        $this->num_pgaraje = $num_pgaraje;
    }

    public function getnum_pgaraje() {
        return $this->num_pgaraje;
    }

    public function setnum_paparcam($num_paparcam) {
        $this->num_paparcam = $num_paparcam;
    }

    public function getnum_paparcam() {
        return $this->num_paparcam;
    }

    public function setdes_orientacion($des_orientacion) {
        $this->des_orientacion = trim($des_orientacion);
    }

    public function getdes_orientacion() {
        return $this->des_orientacion;
    }

    public function setdes_vista($des_vista) {
        $this->des_vista = trim($des_vista);
    }

    public function getdes_vista() {
        return $this->des_vista;
    }

    public function setdes_coficial($des_coficial) {
        $this->des_coficial = trim($des_coficial);
    }

    public function getdes_coficial() {
        return $this->des_coficial;
    }

    public function setsituacion($situacion) {
        $this->situacion = trim($situacion);
    }

    public function getsituacion() {
        return $this->situacion;
    }

    public function setest_estrenar($est_estrenar) {
        $this->est_estrenar = $est_estrenar;
    }

    public function getest_estrenar() {
        if (!($this->est_estrenar instanceof ValoresSN)) {
            $this->est_estrenar = new ValoresSN($this->est_estrenar);
        }
        return $this->est_estrenar;
    }

    public function setestgen($estgen) {
        $this->estgen = trim($estgen);
    }

    public function getestgen() {
        return $this->estgen;
    }

    public function setubicacion($ubicacion) {
        $this->ubicacion = trim($ubicacion);
    }

    public function getubicacion() {
        return $this->ubicacion;
    }

    public function setnlocales($nlocales) {
        $this->nlocales = $nlocales;
    }

    public function getnlocales() {
        return $this->nlocales;
    }

    public function setmsup_patio($msup_patio) {
        $this->msup_patio = $msup_patio;
    }

    public function getmsup_patio() {
        return $this->msup_patio;
    }

    public function setpax($pax) {
        $this->pax = $pax;
    }

    public function getpax() {
        return $this->pax;
    }

    public function setntrasteros($ntrasteros) {
        $this->ntrasteros = $ntrasteros;
    }

    public function getntrasteros() {
        return $this->ntrasteros;
    }

    public function setnascensor($nascensor) {
        $this->nascensor = $nascensor;
    }

    public function getnascensor() {
        return $this->nascensor;
    }

    public function settecho($techo) {
        $this->techo = trim($techo);
    }

    public function gettecho() {
        return $this->techo;
    }

    public function setventana($ventana) {
        $this->ventana = trim($ventana);
    }

    public function getventana() {
        return $this->ventana;
    }

    public function setmt2largofachada($mt2largofachada) {
        $this->mt2largofachada = $mt2largofachada;
    }

    public function getmt2largofachada() {
        return $this->mt2largofachada;
    }

    public function setclimatizacion($climatizacion) {
        $this->climatizacion = trim($climatizacion);
    }

    public function getclimatizacion() {
        return $this->climatizacion;
    }

    public function setcalefaccion($calefaccion) {
        $this->calefaccion = trim($calefaccion);
    }

    public function getcalefaccion() {
        return $this->calefaccion;
    }

    public function setcocina($cocina) {
        $this->cocina = trim($cocina);
    }

    public function getcocina() {
        return $this->cocina;
    }

    public function setaguacaliente($aguacaliente) {
        $this->aguacaliente = trim($aguacaliente);
    }

    public function getaguacaliente() {
        return $this->aguacaliente;
    }

    public function setamueblado($amueblado) {
        $this->amueblado = trim($amueblado);
    }

    public function getamueblado() {
        return $this->amueblado;
    }

    public function setndespachos($ndespachos) {
        $this->ndespachos = $ndespachos;
    }

    public function getndespachos() {
        return $this->ndespachos;
    }

    public function setzonascomunes($zonascomunes) {
        $this->zonascomunes = trim($zonascomunes);
    }

    public function getzonascomunes() {
        return $this->zonascomunes;
    }

    public function setest_destacado($est_destacado) {
        $this->est_destacado = trim($est_destacado);
    }

    public function getest_destacado() {
        return $this->est_destacado;
    }

    public function setest_oferta($est_oferta) {
        $this->est_oferta = $est_oferta;
    }

    public function getest_oferta() {
        if (!($this->est_oferta instanceof ValoresSN)) {
            $this->est_oferta = new ValoresSN($this->est_oferta);
        }
        return $this->est_oferta;
    }

    public function setest_escaparate($est_escaparate) {
        $this->est_escaparate = $est_escaparate;
    }

    public function getest_escaparate() {
        if (!($this->est_escaparate instanceof ValoresSN)) {
            $this->est_escaparate = new ValoresSN($this->est_escaparate);
        }
        return $this->est_escaparate;
    }

    public function setest_cartel($est_cartel) {
        $this->est_cartel = $est_cartel;
    }

    public function getest_cartel() {
        if (!($this->est_cartel instanceof ValoresSN)) {
            $this->est_cartel = new ValoresSN($this->est_cartel);
        }
        return $this->est_cartel;
    }

    public function setcod_cliente($cod_cliente) {
        $this->cod_cliente = $cod_cliente;
    }

    public function getcod_cliente() {
        return $this->cod_cliente;
    }

    public function seturl_banco($url_banco) {
        $this->url_banco = trim($url_banco);
    }

    public function geturl_banco() {
        return $this->url_banco;
    }

    public function setnbalcones($nbalcones) {
        $this->nbalcones = $nbalcones;
    }

    public function getnbalcones() {
        return $this->nbalcones;
    }

    public function setnpisosedif($npisosedif) {
        $this->npisosedif = $npisosedif;
    }

    public function getnpisosedif() {
        return $this->npisosedif;
    }

    public function setnpisosbloque($npisosbloque) {
        $this->npisosbloque = $npisosbloque;
    }

    public function getnpisosbloque() {
        return $this->npisosbloque;
    }

    public function setnum_planta($num_planta) {
        $this->num_planta = trim($num_planta);
    }

    public function getnum_planta() {
        return $this->num_planta;
    }

    public function setr1($r1) {
        $this->r1 = trim($r1);
    }

    public function getr1() {
        return $this->r1;
    }

    public function setr2($r2) {
        $this->r2 = trim($r2);
    }

    public function getr2() {
        return $this->r2;
    }

    public function setcod_postal($cod_postal) {
        $this->cod_postal = trim($cod_postal);
    }

    public function getcod_postal() {
        return $this->cod_postal;
    }

    public function setcertificado_energetico($certificado_energetico) {
        $this->certificado_energetico = trim($certificado_energetico);
    }

    public function getcertificado_energetico() {
        return $this->certificado_energetico;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = trim($Direccion);
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function setgrupos($grupos) {
        $this->grupos = trim($grupos);
    }

    public function getgrupos() {
        return $this->grupos;
    }

    public function setenviar_direccion($enviar_direccion) {
        $this->enviar_direccion = $enviar_direccion;
    }

    public function getenviar_direccion() {
        if (!($this->enviar_direccion instanceof ValoresSN)) {
            $this->enviar_direccion = new ValoresSN($this->enviar_direccion);
        }
        return $this->enviar_direccion;
    }

    public function setsuma_numeros($suma_numeros) {
        $this->suma_numeros = trim($suma_numeros);
    }

    public function getsuma_numeros() {
        return $this->suma_numeros;
    }

    public function settipo_direccion($tipo_direccion) {
        $this->tipo_direccion = $tipo_direccion;
    }

    public function gettipo_direccion() {
        return $this->tipo_direccion;
    }

    public function setescaparate($escaparate) {
        $this->escaparate = trim($escaparate);
    }

    public function getescaparate() {
        return $this->escaparate;
    }

    public function settipo_vacacional($tipo_vacacional) {
        $this->tipo_vacacional = $tipo_vacacional;
    }

    public function gettipo_vacacional() {
        if (!($this->tipo_vacacional instanceof ValoresSN)) {
            $this->tipo_vacacional = new ValoresSN($this->tipo_vacacional);
        }
        return $this->tipo_vacacional;
    }

    public function setreserva_vacacional($reserva_vacacional) {
        $this->reserva_vacacional = $reserva_vacacional;
    }

    public function getreserva_vacacional() {
        if (!($this->reserva_vacacional instanceof ValoresSN)) {
            $this->reserva_vacacional = new ValoresSN($this->reserva_vacacional);
        }
        return $this->reserva_vacacional;
    }

    public function setobservacionRuso($observacionRuso) {
        $this->observacionRuso = trim($observacionRuso);
    }

    public function getobservacionRuso() {
        return $this->observacionRuso;
    }

    public function setdatos($datos) {
        $this->datos = trim($datos);
    }

    public function getdatos() {
        return $this->datos;
    }

    public function setdes_cercanias($des_cercanias) {
        $this->des_cercanias = trim($des_cercanias);
    }

    public function getdes_cercanias() {
        return $this->des_cercanias;
    }

    public function setfinanciacion($financiacion) {
        $this->financiacion = trim($financiacion);
    }

    public function getfinanciacion() {
        return $this->financiacion;
    }

    public function setcod_categoria($cod_categoria) {
        $this->cod_categoria = $cod_categoria;
    }

    public function getcod_categoria() {
        return $this->cod_categoria;
    }

    public function setgoogle_map($google_map) {
        $this->google_map = trim($google_map);
    }

    public function getgoogle_map() {
        return $this->google_map;
    }

    public function setdes_alquiler($des_alquiler) {
        $this->des_alquiler = trim($des_alquiler);
    }

    public function getdes_alquiler() {
        return $this->des_alquiler;
    }

    public function setmandato($mandato) {
        $this->mandato = trim($mandato);
    }

    public function getmandato() {
        return $this->mandato;
    }

    public function setmostrar_coordenadas($mostrar_coordenadas) {
        $this->mostrar_coordenadas = trim($mostrar_coordenadas);
    }

    public function getmostrar_coordenadas() {
        return $this->mostrar_coordenadas;
    }

    public function setcoordenadas($coordenadas) {
        $this->coordenadas = trim($coordenadas);
    }

    public function getcoordenadas() {
        return $this->coordenadas;
    }

    public function setzoom_coordenadas($zoom_coordenadas) {
        $this->zoom_coordenadas = $zoom_coordenadas;
    }

    public function getzoom_coordenadas() {
        return $this->zoom_coordenadas;
    }

    public function setsolo_web($solo_web) {
        $this->solo_web = $solo_web;
    }

    public function getsolo_web() {
        if (!($this->solo_web instanceof ValoresSN)) {
            $this->solo_web = new ValoresSN($this->solo_web);
        }
        return $this->solo_web;
    }

    public function setestadoinmueble($estadoinmueble) {
        $this->estadoinmueble = trim($estadoinmueble);
    }

    public function getestadoinmueble() {
        return $this->estadoinmueble;
    }

}

// END class Inmuebles

