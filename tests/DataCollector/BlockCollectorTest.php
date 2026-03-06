<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\DataCollector;

use Adeliom\EasyBlockBundle\Block\Helper;
use Adeliom\EasyBlockBundle\DataCollector\BlockCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(\Adeliom\EasyBlockBundle\DataCollector\BlockCollector::class)]
final class BlockCollectorTest extends TestCase
{
    public function testCollectorStoresBlocksAndExposesCollectorName(): void
    {
        $helper = $this->createMock(Helper::class);
        $helper->method('getTraces')->willReturn(['block-1' => ['name' => 'Hero']]);

        $collector = new BlockCollector($helper);
        $collector->collect(new Request(), new Response());

        self::assertSame(['block-1' => ['name' => 'Hero']], $collector->getBlocks());
        self::assertSame(BlockCollector::class, $collector->getName());
    }

    public function testCollectorReturnsEmptyArrayWhenNoTraceWasCollected(): void
    {
        $helper = $this->createMock(Helper::class);
        $helper->method('getTraces')->willReturn([]);

        $collector = new BlockCollector($helper);
        $collector->collect(new Request(), new Response());

        self::assertSame([], $collector->getBlocks());
    }
}
