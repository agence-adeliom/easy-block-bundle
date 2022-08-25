<?php

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BlockEvent extends Event
{
    /**
     * @var BlockInterface[]
     */
    private array $blocks = [];

    public function __construct(
        /**
         * @readonly
         */
        private array $settings = []
    ) {
    }

    public function addBlock(BlockInterface $block): void
    {
        $this->blocks[] = $block;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return BlockInterface[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @return mixed
     */
    public function getSetting(string $name, mixed $default = null)
    {
        return $this->settings[$name] ?? $default;
    }
}
