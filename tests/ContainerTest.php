<?php

use Voidzone\PHPsm\Config\XMLAdapter;
use Voidzone\PHPsm\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $adapter = new XMLAdapter();
        $container = new Container($adapter, "./tests/services.xml");
        $this->container = $container;
    }
    
    public function testCanRetrieveAFactoryInstance()
    {
        $this->assertInstanceOf("Voidzone\\PHPsm\\FactoryInterface", $this->container->get("FactoryTest"));
    }
    
    public function testCanRetrieveAServiceInstance()
    {
        $this->assertInstanceOf("Voidzone\\PHPsm\\ServiceInterface", $this->container->get("ServiceTest"));
    }
    
    public function testBuildAliasCanRetrieveAServiceInstance()
    {
        $this->assertInstanceOf("Voidzone\\PHPsm\\FactoryInterface", $this->container->get("AliasService"));
    }
    
    public function testGetANonExistingServiceShouldReturnNull()
    {
        $this->assertNull($this->container->get("NonExistingService"));
    }
    
    public function testCanRetrieveTheSameInstanceTwice()
    {
        $obj = $this->container->get("FactoryTest");
        $hash = spl_object_hash($obj);
        
        $obj2 = $this->container->get("FactoryTest");
        $this->assertEquals($hash, spl_object_hash($obj2));
    }
}

?>
