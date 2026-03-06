<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Block;

use Adeliom\EasyBlockBundle\Block\BlockCollection;
use Adeliom\EasyBlockBundle\Block\BlockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyBlockBundle\Block\BlockCollection::class)]
final class BlockCollectionTest extends TestCase
{
    public function testCollectionIndexesBlocksByConcreteClass(): void
    {
        $first = new class () implements BlockInterface {
            public function getName(): string
            {
                return 'First';
            }

            public function getDescription(): string
            {
                return 'First block';
            }

            public function getIcon(): string|array
            {
                return 'fa-first';
            }

            public function getTemplate(): string
            {
                return '@EasyBlock/first.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function getDefaultSettings(): array
            {
                return [];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }
        };
        $second = new class () implements BlockInterface {
            public function getName(): string
            {
                return 'Second';
            }

            public function getDescription(): string
            {
                return 'Second block';
            }

            public function getIcon(): string|array
            {
                return 'fa-second';
            }

            public function getTemplate(): string
            {
                return '@EasyBlock/second.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function getDefaultSettings(): array
            {
                return [];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }
        };

        $collection = new BlockCollection([$first, $second]);
        $blocks = $collection->getBlocks();

        self::assertCount(2, $blocks);
        self::assertSame($first, $blocks[$first::class]);
        self::assertSame($second, $blocks[$second::class]);
    }
}
