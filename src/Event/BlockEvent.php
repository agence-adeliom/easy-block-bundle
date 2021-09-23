<?php

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BlockEvent extends Event
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var BlockInterface[]
     */
    private $blocks = [];

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
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
     * @param mixed $default
     *
     * @return mixed
     */
    public function getSetting(string $name, $default = null)
    {
        return $this->settings[$name] ?? $default;
    }
}
