<?php

namespace Adeliom\EasyBlockBundle;

use Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyBlockBundle extends Bundle {

    /**
     * @return ExtensionInterface|null The container extension
     */
    public function getContainerExtension()
    {
        return new EasyBlockExtension();
    }
}
