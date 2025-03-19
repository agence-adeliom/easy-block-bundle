<?php

namespace Adeliom\EasyBlockBundle\Event;

use Adeliom\EasyBlockBundle\Block\BlockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\EventDispatcher\Event;

final class PostBlockEvent extends BlockEvent
{
    public function __construct(
        private readonly string $content = '',
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
