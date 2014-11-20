<?php
namespace Voidzone\PHPsm;

use Voidzone\PHPsm\Config\Adapter;
class ServiceManager
{
    const SERVICE = 'service';
    const FACTORY = 'factory';
    const ALIAS   = 'alias';
    
    protected $services = array();
    protected $factories = array();
    protected $aliases = array();
    
    public function __construct(Adapter $adapter, $configFile) 
    {
        if (!is_file($configFile))
            throw new \InvalidArgumentException("Invalid configFile provided, file not found or permission denied");
        
        $config = $adapter->parseConfiguration($configFile)->getConfiguration();
        foreach ($config as $serviceName => $serviceValue) {
            switch ($serviceName) {
                case self::SERVICE:
                    $this->buildService(array_pop($serviceValue));
                    break;
                case self::FACTORY:
                    $this->buildFactory(array_pop($serviceValue));
                    break;
                case self::ALIAS:
                    $this->buildAlias(array_pop($serviceValue));
                    break;
            }
        }
    }
    
    public function set($serviceAlias, $serviceType, $serviceValue)
    {
        switch ($serviceType) {
            case self::ALIAS:
                $this->buildAlias(array('class' => $serviceValue, 'value' => $serviceAlias));
                break;
            case self::FACTORY:
                $this->buildFactory(array('class' => $serviceValue, 'alias' => $serviceAlias));
                break;
            case self::SERVICE:
                $this->buildService(array('class' => $serviceValue, 'alias' => $serviceAlias));
                break;
            default:
                throw new \InvalidArgumentException("Invalid service type, can be SERVICE, FACTORY or ALIAS");
        }
        return $this;
    }
    
    
    public function get($serviceName)
    {
        return $this->findService($serviceName);
    }
    
    public function flushServices()
    {
        $this->factories = array();
        $this->services = array();
        $this->aliases = array();
    }
    
    protected function buildFactory($service) {
        if ($this->validateFactory($service)) {
            if (!is_object($service['class'])) {
                $class = new $service['class'];
            } else {
                $class = $service['class'];
            }
            $class->factory($this);
            $this->factories[$service['alias']] = $class;
        }
    }
    
    protected function buildAlias($service) {
        if ($this->validateAlias($service)) {
            if (is_object($service['class'])) {
                $class = $service['class'];
            } else {
                $class = new $service['class'];
            }
            $this->aliases[$service['value']] = $class;
        }
    }
    
    protected function buildService($service) 
    {
        if ($this->validateService($service)) {
        if (is_object($service['class'])) {
                $class = $service['class'];
            } else {
                $class = new $service['class'];
            }
            $class->createService($this);
            $this->services[$service['alias']] = $class;
        }
    }
    
    private function findService($service)
    {
        if (is_string($service)) {
            $search = $service;
        } elseif (is_array($service)) {
            if (array_key_exists('alias', $service)) {
                $search = $service['alias'];
            } elseif (array_key_exists('value', $service)) {
                $search = $service['value'];
            }
        }
        
        if (array_key_exists($search, $this->factories)) {
            return $this->factories[$search];
        } elseif (array_key_exists($search, $this->services)) {
            return $this->services[$search];
        } elseif (array_key_exists($search, $this->aliases)) {
            return $this->aliases[$search];
        }
    }
    
    private function validateService($service)
    {
        if (!array_key_exists('class', $service))
            throw new \RuntimeException("Invalid configuration file, the service tag must have a 'class' attribute");
        if (!array_key_exists('alias', $service))
            throw new \RuntimeException("Invalid configuration file, the service element must have an 'alias' attribute");
        return true;
    }
    
    private function validateFactory($factory)
    {
        if (!is_array($factory) || !array_key_exists('class', $factory))
            throw new \RuntimeException("Invalid configuration file, the factory element must have a 'class' attribute");
        if (!array_key_exists('alias', $factory))
            throw new \RuntimeException("Invalid configuration file, the factory element must have an 'alias' attribute");
        return true;
    }
    
    private function validateAlias($alias)
    {
        if (!is_array($alias) || !array_key_exists('class', $alias))
            throw new \RuntimeException("Invalid configuration file, the alias element must have a 'class' attribute");
        if (!array_key_exists('value', $alias))
            throw new \RuntimeException("Invalid configuration file, the alias element must have a 'value' attribute");
        return true;
    }
}

?>
