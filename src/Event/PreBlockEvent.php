<?php

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\EventDispatcher\Event;

final class PreBlockEvent extends BlockEvent
{
    public function __construct(
        /**
         * @param ArrayCollection<BlockInterface>|BlockInterface[] $blocks
         */
        private readonly ArrayCollection|array $blocks = [],
    ) {
    }

    public function getBlocks(): ArrayCollection|array
    {
        return $this->blocks;
    }
}

