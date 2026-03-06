<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests;

use Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension;
use Adeliom\EasyBlockBundle\EasyBlockBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyBlockBundle\EasyBlockBundle::class)]
final class EasyBlockBundleTest extends TestCase
{
    public function testBundleCreatesItsContainerExtension(): void
    {
        $extension = (new EasyBlockBundle())->getContainerExtension();

        self::assertInstanceOf(EasyBlockExtension::class, $extension);
        self::assertSame('easy_block', $extension?->getAlias());
    }
}
