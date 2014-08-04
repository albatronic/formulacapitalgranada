<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 12.03.2013 21:52:04
 */

/**
 * @orm:Entity(RrhhPersonal)
 */
class RrhhPersonalEntity extends EntityComunes {

    /**
     * @orm GeneratedValue
     * @orm Id
     * @var integer
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $Id;

    /**
     * @var entities\RrhhDptos
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $IdDpto = '0';

    /**
     * @var entities\RrhhPuestos
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $IdPuesto = '0';

    /**
     * @var string
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $Nombre;

    /**
     * @var string
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $Apellidos;

    /**
     * @var string
     */
    protected $Funciones;

    /**
     * @var string
     */
    protected $DatosPersonales;

    /**
     * @var string
     */
    protected $DatosContacto;

    /**
     * @var entities\Sexos
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $Sexo = '0';

    /**
     * @var entities\ValoresSN
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $MostrarEnPortada = '1';

    /**
     * @var integer
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $OrdenPortada = '0';

    /**
     * @var string
     */
    protected $Telefono1;

    /**
     * @var string
     */
    protected $Telefono2;

    /**
     * @var date
     * @assert NotBlank(groups="RrhhPersonal")
     */
    protected $FechaAntiguedad = '0000-00-00';

    /**
     * @var string
     */
    protected $Web;

    /**
     * @var string
     */
    protected $Email;

    /**
     * @var string
     */
    protected $Facebook;

    /**
     * @var string
     */
    protected $Twitter;

    /**
     * @var string
     */
    protected $Linkedin;

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = '';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'RrhhPersonal';

    /**
     * Nombre de la PrimaryKey
     * @var string
     */
    protected $_primaryKeyName = 'Id';

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
        'RrhhDptos',
        'RrhhPuestos',
        'Sexos',
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

    public function setIdDpto($IdDpto) {
        $this->IdDpto = $IdDpto;
    }

    public function getIdDpto() {
        if (!($this->IdDpto instanceof RrhhDptos))
            $this->IdDpto = new RrhhDptos($this->IdDpto);
        return $this->IdDpto;
    }

    public function setIdPuesto($IdPuesto) {
        $this->IdPuesto = $IdPuesto;
    }

    public function getIdPuesto() {
        if (!($this->IdPuesto instanceof RrhhPuestos))
            $this->IdPuesto = new RrhhPuestos($this->IdPuesto);
        return $this->IdPuesto;
    }

    public function setNombre($Nombre) {
        $this->Nombre = trim($Nombre);
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setApellidos($Apellidos) {
        $this->Apellidos = trim($Apellidos);
    }

    public function getApellidos() {
        return $this->Apellidos;
    }

    public function setFunciones($Funciones) {
        $this->Funciones = trim($Funciones);
    }

    public function getFunciones() {
        return $this->Funciones;
    }

    public function setDatosPersonales($DatosPersonales) {
        $this->DatosPersonales = trim($DatosPersonales);
    }

    public function getDatosPersonales() {
        return $this->DatosPersonales;
    }

    public function setDatosContacto($DatosContacto) {
        $this->DatosContacto = trim($DatosContacto);
    }

    public function getDatosContacto() {
        return $this->DatosContacto;
    }

    public function setSexo($Sexo) {
        $this->Sexo = $Sexo;
    }

    public function getSexo() {
        if (!($this->Sexo instanceof Sexos))
            $this->Sexo = new Sexos($this->Sexo);
        return $this->Sexo;
    }

    public function setMostrarEnPortada($MostrarEnPortada) {
        $this->MostrarEnPortada = $MostrarEnPortada;
    }

    public function getMostrarEnPortada() {
        if (!($this->MostrarEnPortada instanceof ValoresSN))
            $this->MostrarEnPortada = new ValoresSN($this->MostrarEnPortada);
        return $this->MostrarEnPortada;
    }

    public function setOrdenPortada($OrdenPortada) {
        $this->OrdenPortada = $OrdenPortada;
    }

    public function getOrdenPortada() {
        return $this->OrdenPortada;
    }

    public function setTelefono1($Telefono1) {
        $this->Telefono1 = trim($Telefono1);
    }

    public function getTelefono1() {
        return $this->Telefono1;
    }

    public function setTelefono2($Telefono2) {
        $this->Telefono2 = trim($Telefono2);
    }

    public function getTelefono2() {
        return $this->Telefono2;
    }

    public function setFechaAntiguedad($FechaAntiguedad) {
        $date = new Fecha($FechaAntiguedad);
        $this->FechaAntiguedad = $date->getFecha();
        unset($date);
    }

    public function getFechaAntiguedad() {
        $date = new Fecha($this->FechaAntiguedad);
        $ddmmaaaa = $date->getddmmaaaa();
        unset($date);
        return $ddmmaaaa;
    }

    public function setWeb($Web) {
        $this->Web = trim($Web);
    }

    public function getWeb() {
        return $this->Web;
    }

    public function setEmail($Email) {
        $this->Email = trim($Email);
    }

    public function getEmail() {
        return $this->Email;
    }

    public function setFacebook($Facebook) {
        $this->Facebook = trim($Facebook);
    }

    public function getFacebook() {
        return $this->Facebook;
    }

    public function setTwitter($Twitter) {
        $this->Twitter = trim($Twitter);
    }

    public function getTwitter() {
        return $this->Twitter;
    }

    public function setLinkedin($Linkedin) {
        $this->Linkedin = trim($Linkedin);
    }

    public function getLinkedin() {
        return $this->Linkedin;
    }

}

// END class RrhhPersonal
?>