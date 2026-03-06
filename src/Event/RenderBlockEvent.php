<?php

declare(strict_types=1);

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Adeliom\EasyBlockBundle\Entity\Block;
use Symfony\Contracts\EventDispatcher\Event;

final class RenderBlockEvent extends Event
{
    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $assets
     */
    public function __construct(
        private readonly mixed $datas,
        private Block $block,
        private BlockInterface $blockType,
        private array $settings,
        private array $assets,
    ) {
    }

    public function getDatas(): mixed
    {
        return $this->datas;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    public function getBlockType(): BlockInterface
    {
        return $this->blockType;
    }

    public function setBlockType(BlockInterface $blockType): void
    {
        $this->blockType = $blockType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * @param array<string, mixed> $assets
     */
    public function setAssets(array $assets): void
    {
        $this->assets = $assets;
    }
}
