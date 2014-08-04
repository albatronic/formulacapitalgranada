<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 07.02.2013 15:15:14
 */

/**
 * @orm:Entity(CpanEsqueletoWeb)
 */
class CpanEsqueletoWeb extends CpanEsqueletoWebEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Guardo la regla, borro los eventuales ordenes
     * que existieran y los vuelvo a crear.
     * 
     * @return boolean
     */
    public function save() {

        $ok = parent::save();
        if ($ok) {
            $ordenes = new ErpOrdenesArticulos();
            $ordenes->borraOrdenesRegla($this->Id);
            unset($ordenes);
            $this->aplicaRegla();
        }
        return $ok;
    }

    /**
     * Marca de borrado la zona y borra fisícamente los
     * ordenes de articulos involucrados en dicha zona
     * 
     * @return boolean
     */
    public function erase() {

        $idRegla = $this->Id;

        $ok = parent::erase();

        if ($ok) {
            // Borrar los ordenes de los artículos involucrados
            // en la zona/regla borrada        
            $ordenes = new ErpOrdenesArticulos();
            $ordenes->borraOrdenesRegla($idRegla);
            unset($ordenes);
        }

        return $ok;
    }

    public function validaLogico() {
        parent::validaLogico();

        if ($this->NItems < 0)
            $this->NItems = 0;
        if ($this->ItemsPagina < 0)
            $this->ItemsPagina = 0;
    }

    /**
     * Devuelve un array con los id's de reglas aplicables al artículo
     * 
     * @param int $idArticulo El id del articulo
     * @return array Array de reglas aplicables
     */
    public function getReglasArticulo($idArticulo) {

        $articulo = new ErpArticulos($idArticulo);

        $array = array();

        $idEstado1 = $articulo->getIDEstado1()->getId();
        $idEstado2 = $articulo->getIDEstado2()->getId();
        $idEstado3 = $articulo->getIDEstado3()->getId();
        $idEstado4 = $articulo->getIDEstado4()->getId();
        $idEstado5 = $articulo->getIDEstado5()->getId();

        $idMarca = $articulo->getIDMarca()->getId();
        $idCategoria = $articulo->getIDCategoria()->getId();
        $idFamilia = $articulo->getIDFamilia()->getId();
        $idSubfamilia = $articulo->getIDSubfamilia()->getId();
        unset($articulo);

        $filtroEstado = "IdEstado='0'";
        if ($idEstado1 > 0)
            $filtroEstado .= " OR IdEstado='{$idEstado1}'";
        if ($idEstado2 > 0)
            $filtroEstado .= " OR IdEstado='{$idEstado2}'";
        if ($idEstado3 > 0)
            $filtroEstado .= " OR IdEstado='{$idEstado3}'";
        if ($idEstado4 > 0)
            $filtroEstado .= " OR IdEstado='{$idEstado4}'";
        if ($idEstado5 > 0)
            $filtroEstado .= " OR IdEstado='{$idEstado5}'";

        $filtroMarca = "IdMarca='0'";
        if ($idMarca > 0)
            $filtroMarca .= " OR IdMarca='{$idMarca}'";

        $filtroCategoria = "IdCategoria='0'";
        if ($idCategoria > 0)
            $filtroCategoria .= " OR IdCategoria='{$idCategoria}'";

        $filtroFamilia = "IdFamilia='0'";
        if ($idFamilia > 0)
            $filtroFamilia .= " OR IdFamilia='{$idFamilia}'";

        $filtroSubfamilia = "IdSubfamilia='0'";
        if ($idSubfamilia > 0)
            $filtroSubfamilia .= " OR IdSubfamilia='{$idSubfamilia}'";

        $filtro = "({$filtroEstado}) AND ({$filtroMarca}) AND ({$filtroCategoria}) AND ({$filtroFamilia}) AND ({$filtroSubfamilia})";
        $reglas = $this->cargaCondicion("Id", $filtro);
        foreach ($reglas as $regla) {
            $array[] = $regla['Id'];
        }
        return $array;
    }

    /**
     * Genera los ordenes del articulo $idArticulo en base
     * a todas las reglas aplicables al mismo
     * 
     * @param int $idArticulo El id del artículo
     * @return void
     */
    public function aplicaReglasArticulo($idArticulo) {

        $articulo = new ErpArticulos($idArticulo);

        $reglas = $this->getReglasArticulo($idArticulo);

        foreach ($reglas as $regla) {
            $orden = new ErpOrdenesArticulos();
            $filtro = "IdRegla='{$regla}' AND IdArticulo='{$articulo->getId()}'";
            $rows = $orden->cargaCondicion("Id", $filtro);
            if (!$rows[0]["Id"]) {
                $orden->setIdRegla($regla);
                $orden->setIdArticulo($articulo->getId());
                $orden->setObservations($articulo->getDescripcion());
                $orden->create();
            }
        }
        unset($articulo);
    }

    /**
     * Aplica la regla $idRegla, que consiste
     * en crear las entradas en la tabla ErpOrdenesArticulos de los articulos
     * que cumplan las condiciones de la regla
     * 
     * La regla no se aplicará a los artículos que no estén vigentes
     * La regla se aplicará a los artículos publish si y publish no
     * 
     * Si no se indica $idRegla, se aplicará la regla en curso
     * 
     * @param int $idRegla El id de la regla a aplicar
     * @return void
     */
    public function aplicaRegla($idRegla = '') {

        $regla = ($idRegla == '') ? $this : new CpanEsqueletoWeb($idRegla);

        $filtroEstado = ($regla->IdEstado > 0) ?
                "(IdEstado1='{$regla->IdEstado}' OR IdEstado2='{$regla->IdEstado}' OR IdEstado3='{$regla->IdEstado}'  OR IdEstado4='{$regla->IdEstado}'  OR IdEstado5='{$regla->IdEstado}')" :
                "(1)";
        $filtroMarca = ($regla->IdMarca > 0) ?
                "(IdMarca='{$regla->IdMarca}')" :
                "(1)";
        $filtroCategoria = ($regla->IdCategoria > 0) ?
                "(IdCategoria='{$regla->IdCategoria}')" :
                "(1)";
        $filtroFamilia = ($regla->IdFamilia > 0) ?
                "(IdFamilia='{$regla->IdFamilia}')" :
                "(1)";
        $filtroSubfamilia = ($regla->IdSubfamilia > 0) ?
                "(IdSubfamilia='{$regla->IdSubfamilia}')" :
                "(1)";
        $filtroAdicional = ($regla->Filtro != '') ?
                "({$filtroAdicional})" :
                "(1)";

        $filtro = "(Vigente='1') AND {$filtroEstado} AND {$filtroMarca} AND {$filtroCategoria} AND {$filtroFamilia} AND {$filtroSubfamilia} AND {$filtroAdicional}";
        $articulo = new ErpArticulos();
        $rows = $articulo->cargaCondicion("Id,Descripcion", $filtro);
        unset($articulo);

        foreach ($rows as $row) {
            $orden = new ErpOrdenesArticulos();
            $orden->setIdRegla($regla->Id);
            $orden->setIdArticulo($row["Id"]);
            $orden->setObservations($row['Descripcion']);
            $orden->create();
        }
        unset($orden);
    }

}

?>