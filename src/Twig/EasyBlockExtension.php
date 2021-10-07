<?php

namespace Adeliom\EasyBlockBundle\Twig;

use Adeliom\EasyBlockBundle\Block\Helper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EasyBlockExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('easy_block', [Helper::class, 'renderEasyBlock'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
            new TwigFunction('easy_block_assets', [Helper::class, 'includeAssets'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }

}
