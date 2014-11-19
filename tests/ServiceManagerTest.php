<?php

use Voidzone\PHPsm\Config\XMLAdapter;
use Voidzone\PHPsm\ServiceManager;

class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $adapter = new XMLAdapter();
        $container = new ServiceManager($adapter, "./tests/services.xml");
        $this->container = $container;
    }
    
    public function tearDown()
    {
        $this->container->flushServices();
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
    
    public function testCanOverrideAFactoryService()
    {
        $service = $this->container->get('FactoryTest');
        $service->test = 1;
        $hash = spl_object_hash($service);
        $this->container->set('FactoryTest', ServiceManager::FACTORY, $service);
        $object = $this->container->get('FactoryTest');
        $hash2 = spl_object_hash($object);
        
        $this->assertEquals($hash, $hash2);
    }
    
    public function testCanOverrideAService()
    {
        $service = $this->container->get("ServiceTest");
        $service->test = 1;
        $hash = spl_object_hash($service);
        $this->container->set('ServiceTest', ServiceManager::SERVICE, $service);
        $hash2 = spl_object_hash($this->container->get('ServiceTest'));
        $this->assertEquals($hash, $hash2);
    }
    
    public function testCanOverrideAnAlias()
    {
        $service = $this->container->get("AliasService");
        $service->test = 1;
        $hash = spl_object_hash($service);
        $this->container->set('AliasService', ServiceManager::ALIAS, $service);
        $hash2 = spl_object_hash($this->container->get('AliasService'));
        $this->assertEquals($hash, $hash2);
    }
    
    public function testCanGetAServiceByArrayParams()
    {
        $this->assertNotNull($this->container->get(array('alias' => "ServiceTest")));
        $this->assertNotNull($this->container->get(array('value' => "AliasService")));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWithInvalidServiceTypeShouldThrowException()
    {
        $this->container->set("Test", "NoExistingType", new stdClass());
    }
    
    
    
}

?>
