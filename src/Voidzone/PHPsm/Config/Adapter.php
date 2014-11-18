<?php
namespace Voidzone\PHPsm\Config;

abstract class Adapter
{   
    protected $parsedConfiguration;
    
    abstract public function parseConfiguration($config);
    abstract public function getConfiguration();
    
    
}

?>