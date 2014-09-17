<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic
 * @version 1.0 26-nov-2012
 */
class ContenidosController extends ControllerProject {

    protected $entity = "Contenidos";
    protected $template;

    public function IndexAction() {

        switch ($this->request['Entity']) {

            case 'GconContenidos':
                $contenido = Contenidos::getContenido($this->request['IdEntity']);
                if ($contenido->getBlogPublicar()->getIDTipo() == '1') {
                    // Es un post del blog
                    $this->values['contenido'] = Blog::getArticulo($this->request['IdEntity'], 2);
                    $this->values['ultimosPosts'] = Blog::getArticulos(0, false, 1, 3); // Los tres posts más recientes
                    $this->values['servicios'] = Servicios::getServicios(3, true); // Servicios Particulares
                    $this->template = $this->entity . "/post.html.twig";
                } else {
                    // Es un contenido normal
                    $this->values['contenido'] = $contenido;
                    $this->template = $this->entity . "/index.html.twig";
                }
                break;

            case 'GconSecciones':
                // Comprobar si tiene subsecciones
                $seccion = new GconSecciones($this->request['IdEntity']);
                $tieneSubsecciones = $seccion->getNumberOfSubsecciones();

                if (($tieneSubsecciones > 0) and ( $seccion->getMostrarSubsecciones()->getIDTipo() == '1')) {
                    // La seccion tiene subsecciones y son mostrables:
                    // Saco un listado de subsecciones
                    $this->ListadoSubsecciones($this->request['IdEntity']);
                } else {
                    // La seccion no tiene subsecciones. Compruebo si tiene contenidos.
                    // Si tiene solo uno, lo muestro desarrollado. Si tiene varios
                    // saco un listado de contenidos.
                    $nContenidos = $seccion->getNumberOfContenidos();
                    if ($nContenidos == 0) {
                        $this->Seccion($seccion);
                    } elseif ($nContenidos == 1) {
                        $contenido = new GconContenidos();
                        $rows = $contenido->cargaCondicion("Id", "IdSeccion='{$this->request['IdEntity']}'");
                        unset($contenido);
                        $this->ContenidoAislado($rows[0]['Id']);
                    } else {
                        $this->ListadoContenidos($this->request['IdEntity']);
                    }
                }
                unset($seccion);
                break;
        }

        return array(
            'values' => $this->values,
            'template' => $this->template,
        );
    }

    public function Seccion($seccion) {

        $this->values['seccion'] = $seccion;
        $this->template = $this->entity . "/seccion.html.twig";
    }

    /**
     * Muestra un contenido aislado
     */
    public function ContenidoAislado($idContenido) {

        $this->values['listadoContenidos']['contenidos'][0] = Contenidos::getContenidoDesarrollado($idContenido, 8);

        $this->template = $this->entity . "/index.html.twig";
    }

    /**
     * Devuelve los contenidos de la sección $idSeccion en modo
     * listado o desarrollado según el valor de 'ModoMostrarContenidos' de la sección
     * y en el orden indicado en 'CriterioOrdenContenidosHijos'
     * 
     * @param int $idSeccion El id de la seccion 
     */
    public function ListadoContenidos($idSeccion) {

        $seccion = new GconSecciones($idSeccion);

        $nPagina = ($this->request['1']) ? $this->request['1'] : 1;

        $variables = CpanVariables::getVariables('Web', 'Mod', 'GconContenidos');

        switch ($seccion->getModoMostrarContenidos()->getIDTipo()) {
            case '0': // Modo Listado
                $itemsPorPagina = $variables['especificas']['NumContenidosPorPaginaListado'];
                Paginacion::paginar(
                        "GconContenidos", "IdSeccion='{$idSeccion}'", $seccion->getCriterioOrdenContenidosHijos()->getIDTipo(), $nPagina, $itemsPorPagina);

                foreach (Paginacion::getRows() as $row) {
                    $this->values['listadoContenidos']['contenidos'][] = Contenidos::getContenido($row['Id']);
                }

                $this->values['listadoContenidos']['paginacion'] = Paginacion::getPaginacion();

                $this->template = $this->entity . '/listadoContenidos.html.twig';
                break;

            case '1': // Modo desarrollado
                $itemsPorPagina = $variables['especificas']['NumContenidosPorPaginaDesarrollado'];
                Paginacion::paginar(
                        "GconContenidos", "IdSeccion='{$idSeccion}'", $seccion->getCriterioOrdenContenidosHijos()->getIDTipo(), $nPagina, $itemsPorPagina);

                foreach (Paginacion::getRows() as $row) {
                    $this->values['listadoContenidos']['contenidos'][] = $this->getContenidoDesarrollado($row['Id'], 8);
                }

                $this->values['listadoContenidos']['paginacion'] = Paginacion::getPaginacion();

                $this->template = $this->entity . '/index.html.twig';
                break;
        }
        unset($seccion);
    }

    /**
     * Pone en $this->values['listadoSubsecciones'] las subsecciones de la sección $idSeccion
     * @param integer $idSeccion El id de la sección
     */
    public function ListadoSubsecciones($idSeccion) {

        $this->values['listadoSubsecciones'] = $this->getSubsecciones($idSeccion);

        $this->template = $this->entity . '/listadoSubsecciones.html.twig';
    }

    public function ComentarioAction() {

        $comentario = new BlogComentarios();
        $comentario->setEntidad("GconContenidos");
        $comentario->setIdEntidad($this->request['idContenido']);
        $comentario->setIpAddress($_SERVER['REMOTE_ADDR']);
        $comentario->setNombre($this->request['nombre']);
        $comentario->setEmail($this->request['email']);
        $comentario->setComentario($this->request['comentarios']);
        $comentario->setTiempoUnix(time());
        $comentario->setPublish(1);
        $comentario->create();

        $this->request['Entity'] = "GconContenidos";
        $this->request['IdEntity'] = $this->request['idContenido'];
        return $this->IndexAction();
    }

}
