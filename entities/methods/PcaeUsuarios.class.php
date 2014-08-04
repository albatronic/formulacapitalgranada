<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 16.10.2012 16:33:56
 */

/**
 * @orm:Entity(PcaeUsuarios)
 */
class PcaeUsuarios extends PcaeUsuariosEntity {

    public function __toString() {
        return $this->getId();
    }

    /**
     * Devuelve el nombre concatenado con los apellidos
     * @return string
     */
    public function getNombreApellidos() {
        return $this->getNombre() . " " . $this->getApellidos();
    }

    /**
     * Devuelve un array de objetos \PcaeEmpresas
     * a las que tiene acceso el usuario en curso
     *
     * @return array Array de objetos \PcaeEmpresas
     */
    public function getEmpresas() {

        $empresas = array();

        $empUsu = new PcaeEmpresasUsuarios();
        $rows = $empUsu->cargaCondicion("IdEmpresa", "IdUsuario='{$this->Id}'");
        unset($empUsu);

        foreach ($rows as $row)
            $empresas[] = new PcaeEmpresas($row['IdEmpresa']);

        return $empresas;
    }

    /**
     * Devuelve un array con los opciones de menu
     * para el usuario en curso y la empresa $idEmpresa.
     *
     * @param integer $idEmpresa EL id de empresa
     * @return array
     */
    public function getArrayMenu($idEmpresa) {

        $menu = array();

        switch ($this->getPerfilEmpresa($idEmpresa)->getId()) {

            // Super, tiene acceso a todo
            case '1':
                $menu = array('Empresas', 'Usuarios', 'Proyectos');
                break;

            // Administrador de empresa
            case '2':
                $menu = array('Asignar Usuarios');
                break;

            // Acceso, solo tiene permiso de acceso
            case '3':
                break;
        }
        return $menu;
    }

    /**
     * Devuelve un array anidado con las empresas, proyectos
     * y aplicaciones a los que tiene acceso el usuario en curso.
     * 
     * Si se indica el parámetro $idEmpresa, solo se devolverán los
     * accesos a los proyectos y apps de dicha empresa
     *
     * @param integer $idEmpresa El id de empresa
     * @return array
     */
    public function getArrayAccesos($idEmpresa = '') {

        $filtro = "IdUsuario='{$this->Id}'";
        if ($idEmpresa != '')
            $filtro .= "AND IdEmpresa='{$idEmpresa}'";

        $accesos = array();

        $permisos = new PcaePermisos();
        $rows = $permisos->cargaCondicion("DISTINCT IdEmpresa", $filtro, "IdEmpresa ASC");
        unset($permisos);

        foreach ($rows as $row) {

            $empresa = new PcaeEmpresas($row['IdEmpresa']);

            $accesos['empresas'][] = array(
                'id' => $row['IdEmpresa'],
                'RazonSocial' => $empresa->getRazonSocial(),
                'perfil' => $this->getPerfilEmpresa($row['IdEmpresa'])->getPerfil(),
                'url' => $empresa->getHref(),
                'proyectos' => $this->getProyectos($row['IdEmpresa']),
            );
            unset($empresa);
        }

        return $accesos;
    }

    /**
     * Devuelve el objeto PcaePerfiles correspondiente al
     * usuario en curso y la empresa $idEmpresa.
     *
     * @param integer $idEmpresa El id de empresa
     * @return \PcaePerfiles Objeto \PcaePerfiles
     */
    public function getPerfilEmpresa($idEmpresa) {

        $empUsu = new PcaeEmpresasUsuarios();
        $rows = $empUsu->cargaCondicion("IdPerfil", "IdEmpresa='{$idEmpresa}' AND IdUsuario='{$this->Id}'");
        unset($empUsu);

        return new PcaePerfiles($rows[0]['IdPerfil']);
    }

    /**
     * Devuelve un array con los proyecots y apps a los 
     * que tiene acceso el usuario en curso
     * 
     * @param integer $idEmpresa El id de la empresa
     * @return array Array de proyectos y apps
     */
    public function getProyectos($idEmpresa) {
        $array = array();

        $filtro = "IdUsuario='{$this->Id}' AND IdEmpresa='{$idEmpresa}'";

        $permisos = new PcaePermisos();
        $rows = $permisos->cargaCondicion("DISTINCT IdProyecto", $filtro, "IdProyecto,IdApp ASC");
        unset($permisos);

        foreach ($rows as $row) {
            $proyecto = new PcaeProyectos($row['IdProyecto']);
            $array[] = array(
                'idProyecto' => $row['IdProyecto'],
                'titulo' => $proyecto->getProyecto(),
                'apps' => $this->getApps($row['IdProyecto']),
            );
            unset($proyecto);
        }

        return $array;
    }

    /**
     * Devuelve un array con todas las apps del proyecto $idProyecto
     * a las que tiene acceso el usuario
     * 
     * @param type $idProyecto
     * @return array Array de apps
     */
    public function getApps($idProyecto) {

        $array = array();

        $filtro = "IdUsuario='{$this->Id}' AND IdProyecto='{$idProyecto}'";

        $permisos = new PcaePermisos();
        $rows = $permisos->cargaCondicion("IdApp", $filtro, "IdApp ASC");
        unset($permisos);

        foreach ($rows as $row) {
            $proApps = new PcaeProyectosApps();
            $proApp = $proApps->cargaCondicion("Id","IdProyecto='{$idProyecto}' AND IdApp='{$row['IdApp']}'");
            $proApp = new PcaeProyectosApps($proApp[0]['Id']);
            $array[] = array(
                'idApp' => $row['IdApp'],
                'titulo' => $proApp->getIdApp()->getAplicacion(),
                'url' => $proApp->getIdApp()->getUrl(),
                'md5' => $proApp->getPrimaryKeyMD5(),
            );
            unset($proApp);
        }

        return $array;
    }

}

?>