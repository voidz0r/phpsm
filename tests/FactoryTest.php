<?php
use Voidzone\PHPsm\FactoryInterface;
use Voidzone\PHPsm\ServiceManager;

class FactoryTest implements FactoryInterface
{
    public function factory(ServiceManager $sm)
    {
        return true; 
    }
}

?>