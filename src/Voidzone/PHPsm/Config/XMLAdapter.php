<?php
namespace Voidzone\PHPsm\Config;

class XMLAdapter extends Adapter
{
    public function parseConfiguration($config)
    {
        $retConfig = array();
        
        $xml = simplexml_load_file($config);
        if (count($xml->factories) > 0) {
            foreach ($xml->factories->factory as $factory) {
                $retConfig['factory'][] = array(
                    'class' => (string)$factory->attributes()->class,
                    'alias' => (string)$factory->attributes()->alias  
                );
            }
        }
        if (count($xml->services) > 0) {
            foreach ($xml->services->service as $service) {
                $retConfig['service'][] = array(
                    'class' => (string)$service->attributes()->class,
                    'alias' => (string)$service->attributes()->alias  
                );
            }
        }
        if (count($xml->aliases) > 0) {
            foreach ($xml->aliases->alias as $alias) {
                $retConfig['alias'][] = array(
                    'class' => (string)$alias->attributes()->class,
                    'value' => (string)$alias->attributes()->value
                );
            }    
        }
        $this->parsedConfiguration = $retConfig;
        return $this;
    }
    
    public function getConfiguration()
    {
        return $this->parsedConfiguration;
    }
}

?>