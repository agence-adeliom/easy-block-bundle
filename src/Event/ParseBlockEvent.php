<?php

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\EventDispatcher\Event;

final class ParseBlockEvent extends BlockEvent
{
    public function __construct(
        protected readonly array $settings = []
    ) {
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getSetting(string $name, mixed $default = null): array
    {
        return $this->settings[$name] ?? $default;
    }
}
