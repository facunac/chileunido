<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Seguimientotest
 *
 * @author pancha
 */
class SeguimientoTest extends UnitTestCase{

    function testSeguimientoExist() {
        loadModel('Seguimiento');
        $seguimiento = &new Seguimiento();
        $this->assertEqual($seguimiento->name, 'Seguimiento');
        $this->assertEqual($seguimiento->primaryKey, 'num_evento');
    }
}
?>
