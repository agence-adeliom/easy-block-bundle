<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\DependencyInjection;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension;
use Adeliom\EasyBlockBundle\Tests\Fixtures\Entity\TestBlock;
use Adeliom\EasyBlockBundle\Tests\Fixtures\Repository\TestBlockRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(\Adeliom\EasyBlockBundle\DependencyInjection\EasyBlockExtension::class)]
final class EasyBlockExtensionTest extends TestCase
{
    public function testExtensionLoadsParametersServicesAndAutoconfiguration(): void
    {
        $container = new ContainerBuilder();
        $extension = new EasyBlockExtension();

        $extension->load([[
            'block_class' => TestBlock::class,
            'block_repository' => TestBlockRepository::class,
        ]], $container);

        self::assertSame('easy_block', $extension->getAlias());
        self::assertSame(TestBlock::class, $container->getParameter('easy_block.class'));
        self::assertSame(TestBlockRepository::class, $container->getParameter('easy_block.repository'));
        self::assertTrue($container->hasDefinition('Adeliom\\EasyBlockBundle\\Block\\BlockCollection'));
        self::assertTrue($container->hasDefinition('Adeliom\\EasyBlockBundle\\Twig\\EasyBlockExtension'));
        self::assertTrue($container->hasAlias('easy_block.twig_extension'));

        self::assertArrayHasKey(BlockInterface::class, $container->getAutoconfiguredInstanceof());
    }
}
