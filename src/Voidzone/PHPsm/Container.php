<?php
namespace Voidzone\PHPsm;

use Voidzone\PHPsm\Config\Adapter;
class Container
{
    protected $services = array();
    protected $factories = array();
    protected $aliases = array();
    
    public function __construct(Adapter $adapter, $configFile) {
        if (!is_file($configFile))
            throw new \InvalidArgumentException("Invalid configFile provided, file not found or permission denied");
        
        $config = $adapter->parseConfiguration($configFile)->getConfiguration();
        foreach ($config as $serviceName => $serviceValue) {
            switch ($serviceName) {
                case 'service':
                    $this->buildService(array_pop($serviceValue));
                    break;
                case 'factory':
                    $this->buildFactory(array_pop($serviceValue));
                    break;
                case 'alias':
                    $this->buildAlias(array_pop($serviceValue));
                    break;
            }
        }
    }
    
    public function get($serviceName)
    {
        if (array_key_exists($serviceName, $this->services))
            return $this->services[$serviceName];
        elseif (array_key_exists($serviceName, $this->factories))
            return $this->factories[$serviceName];
        elseif (array_key_exists($serviceName, $this->aliases))
            return $this->aliases[$serviceName];
    }
    
    protected function buildFactory($service) {
        if (!array_key_exists('class', $service))
            throw new \RuntimeException("Invalid configuration file, the factory element must have a 'class' attribute");
        if (!array_key_exists('alias', $service))
            throw new \RuntimeException("Invalid configuration file, the factory element must have an 'alias' attribute");
        if (!array_key_exists($service['class'], $this->factories)) {
            $class = new $service['class'];
            if (!$class instanceof FactoryInterface)
                throw new \RuntimeException("Invalid class, it must implements FactoryInterface interface");
            $class->factory();
            $this->factories[$service['alias']] = $class;
        }
    }
    
    protected function buildAlias($service) {
        if (!array_key_exists('class', $service))
            throw new \RuntimeException("Invalid configuration file, the alias element must have a 'class' attribute");
        if (!array_key_exists('value', $service))
            throw new \RuntimeException("Invalid configuration file, the alias element must have a 'value' attribute");
        
        if (!array_key_exists($service['value'], $this->aliases)) {
            $class = new $service['class'];
            $this->aliases[$service['value']] = $class;
        }
    }
    
    protected function buildService($service) 
    {
        if (!array_key_exists('class', $service))
            throw new \RuntimeException("Invalid configuration file, the service tag must have a 'class' attribute");
        if (!array_key_exists('alias', $service))
            throw new \RuntimeException("Invalid configuration file, the service element must have an 'alias' attribute");

        if (!array_key_exists($service['class'], $this->services)) {
            $class = new $service['class'];
            if (!$class instanceof ServiceInterface)
                throw new \RuntimeException("Invalid class, it must implements ServiceInterface interface");
            $class->createService();
            $this->services[$service['alias']] = $class;
        }
    }
}

?>