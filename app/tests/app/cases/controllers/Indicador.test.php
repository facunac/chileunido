<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnotacionesControllertest
 *
 * @author pancha
 */
 require_once(TESTS.'/app/cases/views/indicador/indicador.test.php');

class IndicadorControllerTest extends UnitTestCase {
    //put your code here

    function IndicadoresControllerTest()
    {
        $this->UnitTestCase("Indicadores Controller test case");
    }
    function testIndicadoresControllerExist() {
       loadController('Indicador');
       $indicadores = &new IndicadorController();
       $this->assertNotNull($indicadores);
       
    }
    function testIndicadoresControllerHasName() {

       loadController('Indicadores');
       $indicadores = &new IndicadorController();
       $this->assertNotNull($indicadores->name,"Indicadores no tiene name");
       $this->assertEqual($indicadores->name,"Indicador");
    }
    function testIndicadoresHasIndex() {

       loadController('Indicadores');
       $indicadores = &new IndicadorController();

      // $indicadores->index();
       $this->assertTrue(true);
    }



    function testIndicadoresVariables()
    {
       
    }
   


}
?>
