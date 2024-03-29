<?php

namespace Adeliom\EasyBlockBundle\Block;

class BlockCollection
{
    /** @var BlockInterface[] */
    protected $blocks = [];

    public function __construct(iterable $blocks)
    {
        foreach ($blocks as $block) {
            $this->blocks[$block::class] = $block;
        }
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
