<?php
use Voidzone\PHPsm\FactoryInterface;

class FactoryTest implements FactoryInterface
{
    public function factory()
    {
        return true; 
    }
}

?>