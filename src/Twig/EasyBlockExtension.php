<?php

namespace Adeliom\EasyBlockBundle\Twig;

use Adeliom\EasyBlockBundle\Block\Helper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EasyBlockExtension extends AbstractExtension
{
    public function __construct(
        private readonly Helper $helper,
    )
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('easy_block', $this->helper->renderEasyBlock(...), ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
            new TwigFunction('easy_block_assets', $this->helper->includeAssets(...), ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }
}
