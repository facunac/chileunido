<?php
require_once 'PHPUnit/Framework.php';
class IndicadoresTest extends PHPUnit_Framework_TestCase
{
    public function testIndicadoresPageExist()
    {
        $this->assertTrue(include "derivaciones/index.php");

    }
}

?>
