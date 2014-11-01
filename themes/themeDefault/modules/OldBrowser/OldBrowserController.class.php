<?php
/**
 * Description of OldBrowserController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 06-nov-2012
 */
class OldBrowserController extends ControllerProject {

    var $entity = "OldBrowser";
    
    public function IndexAction() {
        
        $this->setVariables('Web', 'Pro');

        $this->values['datos']['logo'] = $this->varWeb['Pro']['globales']['logoOldBrowser'];
        $this->values['datos']['empresa'] = $this->varWeb['Pro']['globales']['empresa'];
        
        return array(
            'template' => $this->entity . "/index.html.twig",
            'values' => $this->values,
        );
    }
}

?>
