<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indicadorweb
 *
 * @author pancha
 */
class IndicadorWeb extends WebTestCase {
    //put your code here
   function testIndicadorView()
   {
       $this->assertTrue($this->get("http://localhost:8080/indicador/index.thtml"));
   }
   function testLogin()
   {
   
        
        $this->get('http://localhost:8080/');
       // $this->assertField('data[FormLogin][nom_login]');
       // $this->assertField('data[FormLogin][pas_voluntario]');
       // $this->assertField('Entrar');

        $this->setField('data[FormLogin][nom_login]','administrator');
        $this->setField('data[FormLogin][pas_voluntario]','hola.123');
        $this->clickSubmit("Entrar");
        $this->assertText("Usuario");
        $this->assertText("Comunicate");
        
   

   }
}
?>
