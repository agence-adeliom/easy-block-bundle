<?php

namespace Adeliom\EasyBlockBundle\DataCollector;

use Adeliom\EasyBlockBundle\Block\Helper;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlockCollector extends AbstractDataCollector
{
    public function __construct(protected Helper $blockHelper)
    {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data['blocks'] = $this->blockHelper->getTraces();
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->data['blocks'] ?: [];
    }

    public function getName(): string
    {
        return self::class;
    }
}
