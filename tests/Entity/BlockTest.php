<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Entity;

use Adeliom\EasyBlockBundle\Entity\Block;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyBlockBundle\Entity\Block::class)]
final class BlockTest extends TestCase
{
    public function testBlockStoresBusinessProperties(): void
    {
        $block = new Block();
        $block->setName('Homepage hero');
        $block->setKey('homepage-hero');
        $block->setType('hero');
        $block->setSettings(['theme' => 'light']);
        $block->setStatus(true);

        self::assertSame('Homepage hero', $block->getName());
        self::assertSame('homepage-hero', $block->getKey());
        self::assertSame('hero', $block->getType());
        self::assertSame(['theme' => 'light'], $block->getSettings());
        self::assertTrue($block->getStatus());
        self::assertNotNull($block->getCreatedAt());
    }
}
