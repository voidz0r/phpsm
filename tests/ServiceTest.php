<?php

use Voidzone\PHPsm\ServiceInterface;
use Voidzone\PHPsm\ServiceManager;

class ServiceTest implements ServiceInterface
{
    public function createService(ServiceManager $sm)
    {
        return true;
    }
}

?>