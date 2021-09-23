<?php

namespace Adeliom\EasyBlockBundle\Repository;

use Adeliom\EasyBlockBundle\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;


class BlockRepository extends ServiceEntityRepository {

    /**
     * @return QueryBuilder
     */
    public function getPublishedQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('block')
            ->where('block.status = :state')
        ;

        $qb->setParameter('state', true);
        return $qb;
    }

    /**
     * @return Block[]
     */
    public function getActive()
    {
        $qb = $this->getPublishedQuery();
        return $qb->getQuery()
            ->getResult();
    }


    /**
     * @return Block[]
     */
    public function getByType(string $type)
    {
        $qb = $this->getPublishedQuery();
        $qb->andWhere('block.type = :type')
            ->setParameter('type', $type);

        return $qb->getQuery()
            ->getResult();
    }
}
