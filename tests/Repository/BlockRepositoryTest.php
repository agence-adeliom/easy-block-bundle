<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Tests\Repository;

use Adeliom\EasyBlockBundle\Repository\BlockRepository;
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
        $builder = $this->createConfiguredBuilder();
        $repository = new BlockRepositoryHarness($builder, ['block']);

        self::assertSame(['block'], $repository->getActive());
    }

    public function testGetByTypeAddsTypeFilterAndReturnsResults(): void
    {
        $builder = $this->createConfiguredBuilder();
        $builder->expects(self::once())->method('andWhere')->with('block.type = :type')->willReturnSelf();
        $calls = [];
        $builder->expects(self::exactly(2))->method('setParameter')
            ->willReturnCallback(function (string $key, mixed $value) use (&$calls, $builder): QueryBuilder {
                $calls[] = [$key, $value];

                return $builder;
            });

        $repository = new BlockRepositoryHarness($builder, ['hero']);

        self::assertSame(['hero'], $repository->getByType('hero'));
        self::assertSame([['state', true], ['type', 'hero']], $calls);
    }

    private function createConfiguredBuilder(): QueryBuilder
    {
        $builder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['where', 'setParameter', 'andWhere'])
            ->getMock();

        $builder->method('where')->willReturnSelf();
        $builder->method('setParameter')->willReturnSelf();
        $builder->method('andWhere')->willReturnSelf();

        return $builder;
    }
}

final class BlockRepositoryHarness extends BlockRepository
{
    public function __construct(
        private QueryBuilder $builder,
        private array $queryResult = [],
    ) {
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return $this->builder;
    }

    protected function executeQueryResult(QueryBuilder $qb): array
    {
        return $this->queryResult;
    }
}
