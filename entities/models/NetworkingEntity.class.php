<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 18.01.2013 11:36:41
 */

/**
 * @orm:Entity(Networking)
 */
class NetworkingEntity extends EntityComunes {

    /**
     * @orm GeneratedValue
     * @orm Id
     * @var integer
     * @assert NotBlank(groups="Networking")
     */
    protected $Id;

    /**
     * @var string
     * @assert NotBlank(groups="Networking")
     */
    protected $Titulo;

    /**
     * @var string
     * @assert NotBlank(groups="Networking")
     */
    protected $IdUsuario;

    /**
     * @var string
     * @assert NotBlank(groups="Networking")
     */
    protected $Url;

    /**
     * @var integer
     * @assert NotBlank(groups="Networking")
     */
    protected $NumeroItems = '0';

    /**
     * @var entities\ValoresSN
     * @assert NotBlank(groups="Networking")
     */
    protected $MostrarAvatar = '0';

    /**
     * @var string
     */
    protected $Mensaje;

    /**
     * @var entities\ValoresSN
     * @assert NotBlank(groups="Networking")
     */
    protected $BotonEnviar = '0';

    /**
     * @var entities\RedesSocialesModosMostrar
     * @assert NotBlank(groups="Networking")
     */
    protected $ModoMostrar = '0';

    /**
     * @var entities\RedesSocialesFonts
     * @assert NotBlank(groups="Networking")
     */
    protected $Font = '0';

    /**
     * @var string
     */
    protected $Class;

    /**
     * @var entities\RedesSocialesActions
     * @assert NotBlank(groups="Networking")
     */
    protected $Action = '0';

    /**
     * @var integer
     * @assert NotBlank(groups="Networking")
     */
    protected $Ancho = '0';

    /**
     * @var integer
     * @assert NotBlank(groups="Networking")
     */
    protected $Alto = '0';

    /**
     * @var entities\RedesSocialesSizes
     * @assert NotBlank(groups="Networking")
     */
    protected $Size = '0';

    /**
     * @var entities\RedesSocialesColoresFondo
     * @assert NotBlank(groups="Networking")
     */
    protected $ColorFondo = '0';

    /**
     * @var string
     */
    protected $ColorBorde;

    /**
     * @var entities\RedesSocialesCounts
     * @assert NotBlank(groups="Networking")
     */
    protected $Count = '0';

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = '';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'Networking';

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
        'ValoresSN',
        'RedesSocialesModosMostrar',
        'RedesSocialesFonts',
        'RedesSocialesActions',
        'RedesSocialesSizes',
        'RedesSocialesColoresFondo',
        'RedesSocialesCounts',
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

    public function setTitulo($Titulo) {
        $this->Titulo = trim($Titulo);
    }

    public function getTitulo() {
        return $this->Titulo;
    }

    public function setIdUsuario($IdUsuario) {
        $this->IdUsuario = trim($IdUsuario);
    }

    public function getIdUsuario() {
        return $this->IdUsuario;
    }

    public function setUrl($Url) {
        $this->Url = trim($Url);
    }

    public function getUrl() {
        return $this->Url;
    }

    public function setNumeroItems($NumeroItems) {
        $this->NumeroItems = $NumeroItems;
    }

    public function getNumeroItems() {
        return $this->NumeroItems;
    }

    public function setMostrarAvatar($MostrarAvatar) {
        $this->MostrarAvatar = $MostrarAvatar;
    }

    public function getMostrarAvatar() {
        if (!($this->MostrarAvatar instanceof ValoresSN))
            $this->MostrarAvatar = new ValoresSN($this->MostrarAvatar);
        return $this->MostrarAvatar;
    }

    public function setMensaje($Mensaje) {
        $this->Mensaje = trim($Mensaje);
    }

    public function getMensaje() {
        return $this->Mensaje;
    }

    public function setBotonEnviar($BotonEnviar) {
        $this->BotonEnviar = $BotonEnviar;
    }

    public function getBotonEnviar() {
        if (!($this->BotonEnviar instanceof ValoresSN))
            $this->BotonEnviar = new ValoresSN($this->BotonEnviar);
        return $this->BotonEnviar;
    }

    public function setModoMostrar($ModoMostrar) {
        $this->ModoMostrar = $ModoMostrar;
    }

    public function getModoMostrar() {
        if (!($this->ModoMostrar instanceof RedesSocialesModosMostrar))
            $this->ModoMostrar = new RedesSocialesModosMostrar($this->ModoMostrar);
        return $this->ModoMostrar;
    }

    public function setFont($Font) {
        $this->Font = $Font;
    }

    public function getFont() {
        if (!($this->Font instanceof RedesSocialesFonts))
            $this->Font = new RedesSocialesFonts($this->Font);
        return $this->Font;
    }

    public function setClass($Class) {
        $this->Class = trim($Class);
    }

    public function getClass() {
        return $this->Class;
    }

    public function setAction($Action) {
        $this->Action = $Action;
    }

    public function getAction() {
        if (!($this->Action instanceof RedesSocialesActions))
            $this->Action = new RedesSocialesActions($this->Action);
        return $this->Action;
    }

    public function setAncho($Ancho) {
        $this->Ancho = $Ancho;
    }

    public function getAncho() {
        return $this->Ancho;
    }

    public function setAlto($Alto) {
        $this->Alto = $Alto;
    }

    public function getAlto() {
        return $this->Alto;
    }

    public function setSize($Size) {
        $this->Size = $Size;
    }

    public function getSize() {
        if (!($this->Size instanceof RedesSocialesSizes))
            $this->Size = new RedesSocialesSizes($this->Size);
        return $this->Size;
    }

    public function setColorFondo($ColorFondo) {
        $this->ColorFondo = $ColorFondo;
    }

    public function getColorFondo() {
        if (!($this->ColorFondo instanceof RedesSocialesColoresFondo))
            $this->ColorFondo = new RedesSocialesColoresFondo($this->ColorFondo);
        return $this->ColorFondo;
    }

    public function setColorBorde($ColorBorde) {
        $this->ColorBorde = trim($ColorBorde);
    }

    public function getColorBorde() {
        return $this->ColorBorde;
    }

    public function setCount($Count) {
        $this->Count = $Count;
    }

    public function getCount() {
        if (!($this->Count instanceof RedesSocialesCounts))
            $this->Count = new RedesSocialesCounts($this->Count);
        return $this->Count;
    }

}

// END class Networking
?>