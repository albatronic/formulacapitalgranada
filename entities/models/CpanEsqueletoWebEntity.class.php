<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 07.02.2013 15:32:56
 */

/**
 * @orm:Entity(CpanEsqueletoWeb)
 */
class CpanEsqueletoWebEntity extends EntityComunes {

    /**
     * @orm GeneratedValue
     * @orm Id
     * @var integer
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $Id;

    /**
     * @var string
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $Controller;

    /**
     * @var integer
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $Zona = '0';

    /**
     * @var entities\ErpArticulosEstados
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $IdEstado = '0';

    /**
     * @var entities\ErpMarcas
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $IdMarca = '0';

    /**
     * @var entities\ErpFamilias
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $IdCategoria = '0';

    /**
     * @var entities\ErpFamilias
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $IdFamilia = '0';

    /**
     * @var entities\ErpFamilias
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $IdSubfamilia = '0';

    /**
     * @var string
     */
    protected $Filtro;

    /**
     * @var integer
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $NItems = '0';

    /**
     * @var integer
     * @assert NotBlank(groups="CpanEsqueletoWeb")
     */
    protected $ItemsPagina = '0';

    /**
     * Nombre de la conexion a la BD
     * @var string
     */
    protected $_conectionName = '';

    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'CpanEsqueletoWeb';

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
        'ErpArticulosEstados',
        'ErpMarcas',
        'ErpFamilias',
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

    public function setController($Controller) {
        $this->Controller = trim($Controller);
    }

    public function getController() {
        return $this->Controller;
    }

    public function setZona($Zona) {
        $this->Zona = $Zona;
    }

    public function getZona() {
        return $this->Zona;
    }

    public function setIdEstado($IdEstado) {
        $this->IdEstado = $IdEstado;
    }

    public function getIdEstado() {
        if (!($this->IdEstado instanceof ErpArticulosEstados))
            $this->IdEstado = new ErpArticulosEstados($this->IdEstado);
        return $this->IdEstado;
    }

    public function setIdMarca($IdMarca) {
        $this->IdMarca = $IdMarca;
    }

    public function getIdMarca() {
        if (!($this->IdMarca instanceof ErpMarcas))
            $this->IdMarca = new ErpMarcas($this->IdMarca);
        return $this->IdMarca;
    }

    public function setIdCategoria($IdCategoria) {
        $this->IdCategoria = $IdCategoria;
    }

    public function getIdCategoria() {
        if (!($this->IdCategoria instanceof ErpFamilias))
            $this->IdCategoria = new ErpFamilias($this->IdCategoria);
        return $this->IdCategoria;
    }

    public function setIdFamilia($IdFamilia) {
        $this->IdFamilia = $IdFamilia;
    }

    public function getIdFamilia() {
        if (!($this->IdFamilia instanceof ErpFamilias))
            $this->IdFamilia = new ErpFamilias($this->IdFamilia);
        return $this->IdFamilia;
    }

    public function setIdSubfamilia($IdSubfamilia) {
        $this->IdSubfamilia = $IdSubfamilia;
    }

    public function getIdSubfamilia() {
        if (!($this->IdSubfamilia instanceof ErpFamilias))
            $this->IdSubfamilia = new ErpFamilias($this->IdSubfamilia);
        return $this->IdSubfamilia;
    }

    public function setFiltro($Filtro) {
        $this->Filtro = trim($Filtro);
    }

    public function getFiltro() {
        return $this->Filtro;
    }

    public function setNItems($NItems) {
        $this->NItems = $NItems;
    }

    public function getNItems() {
        return $this->NItems;
    }

    public function setItemsPagina($ItemsPagina) {
        $this->ItemsPagina = $ItemsPagina;
    }

    public function getItemsPagina() {
        return $this->ItemsPagina;
    }
}

// END class CpanEsqueletoWeb
?>