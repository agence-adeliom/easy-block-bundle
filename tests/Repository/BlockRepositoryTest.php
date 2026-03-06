<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Repository;

use Adeliom\EasyBlockBundle\Repository\BlockRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyBlockBundle\Repository\BlockRepository::class)]
final class BlockRepositoryTest extends TestCase
{
    public function testGetPublishedQueryFiltersOnPublishedState(): void
    {
        $builder = $this->createConfiguredBuilder();
        $builder->expects(self::once())->method('where')->with('block.status = :state')->willReturnSelf();
        $builder->expects(self::once())->method('setParameter')->with('state', true)->willReturnSelf();

        $repository = new BlockRepositoryHarness($builder);

        self::assertSame($builder, $repository->getPublishedQuery());
    }

    public function testGetActiveReturnsQueryResults(): void
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->expects(self::once())->method('getResult')->willReturn(['block']);

        $builder = $this->createConfiguredBuilder($query);
        $repository = new BlockRepositoryHarness($builder);

        self::assertSame(['block'], $repository->getActive());
    }

    public function testGetByTypeAddsTypeFilterAndReturnsResults(): void
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->expects(self::once())->method('getResult')->willReturn(['hero']);

        $builder = $this->createConfiguredBuilder($query);
        $builder->expects(self::once())->method('andWhere')->with('block.type = :type')->willReturnSelf();
        $calls = [];
        $builder->expects(self::exactly(2))->method('setParameter')
            ->willReturnCallback(function (string $key, mixed $value) use (&$calls, $builder): QueryBuilder {
                $calls[] = [$key, $value];

                return $builder;
            });

        $repository = new BlockRepositoryHarness($builder);

        self::assertSame(['hero'], $repository->getByType('hero'));
        self::assertSame([['state', true], ['type', 'hero']], $calls);
    }

    private function createConfiguredBuilder(?AbstractQuery $query = null): QueryBuilder
    {
        $builder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['where', 'setParameter', 'andWhere', 'getQuery'])
            ->getMock();

        $builder->method('where')->willReturnSelf();
        $builder->method('setParameter')->willReturnSelf();
        $builder->method('andWhere')->willReturnSelf();
        $builder->method('getQuery')->willReturn($query ?? $this->createMock(AbstractQuery::class));

        return $builder;
    }
}

final class BlockRepositoryHarness extends BlockRepository
{
    public function __construct(private QueryBuilder $builder)
    {
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return $this->builder;
    }
}
