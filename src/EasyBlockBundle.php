<?php

namespace Adeliom\EasyBlockBundle;

use Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyBlockBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null The container extension
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EasyBlockExtension();
    }
}
