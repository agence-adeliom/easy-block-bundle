<?php

namespace Adeliom\EasyBlockBundle;

use Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyBlockBundle extends Bundle {

    public function getContainerExtension()
    {
        return new EasyBlockExtension();
    }
}
