<?php

/**
 * GENERAR EL CODIGO PHP CON EL CONTROL DE ACCIONES DE UNA TABLA
 *
 * NECESITA APOYARSE EN LA CLASE 'TableDescriptor'
 *
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC, SL 15.03.2011
 * @version 1.0
 */
class ControllerBuilder {

    static function getController($tableName, $prefijo = '') {
        
        $sinPrefijo = ucwords(str_replace($prefijo, "", $tableName));

        $buf = "<?php\n";
        $buf .= "/**\n";
        $buf .= "* CONTROLLER FOR " . $sinPrefijo . "\n";
        $buf .= "* @copyright: GRUPO TREVENQUE \n* @date " . date('d.m.Y H:i:s') . "\n\n";
        $buf .= "* Extiende a la clase controller\n";
        $buf .= "*/\n\n";
        $buf .= "class " . $sinPrefijo . "Controller extends Controller {\n\n";
        $buf .= "\tprotected \$entity = \"" . $sinPrefijo . "\";\n";
        $buf .= "\tprotected \$parentEntity = \"\";\n\n";
        $buf .= "\tpublic function IndexAction() {\n";
        $buf .= "\t\treturn \$this->listAction();\n";
        $buf .= "\t}\n";
        $buf .= "}\n";

        return $buf;
    }

}
