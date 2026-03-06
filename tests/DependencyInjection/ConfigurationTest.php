<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\DependencyInjection;

use Adeliom\EasyBlockBundle\DependencyInjection\Configuration;
use Adeliom\EasyBlockBundle\Repository\BlockRepository;
use Adeliom\EasyBlockBundle\Tests\Fixtures\Entity\TestBlock;
use Adeliom\EasyBlockBundle\Tests\Fixtures\Repository\TestBlockRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(\Adeliom\EasyBlockBundle\DependencyInjection\Configuration::class)]
final class ConfigurationTest extends TestCase
{
    public function testConfigurationAcceptsValidClassesAndAppliesDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'block_class' => TestBlock::class,
        ]]);

        self::assertSame(TestBlock::class, $config['block_class']);
        self::assertSame(BlockRepository::class, $config['block_repository']);
    }

    public function testConfigurationAcceptsCustomRepository(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'block_class' => TestBlock::class,
            'block_repository' => TestBlockRepository::class,
        ]]);

        self::assertSame(TestBlockRepository::class, $config['block_repository']);
    }

    public function testConfigurationRejectsInvalidBlockClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), [[
            'block_class' => \stdClass::class,
        ]]);
    }

    public function testConfigurationRejectsInvalidRepositoryClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), [[
            'block_class' => TestBlock::class,
            'block_repository' => \stdClass::class,
        ]]);
    }
}
