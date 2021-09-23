<?php

namespace Adeliom\EasyBlockBundle\Twig;

use Adeliom\EasyBlockBundle\Block\BlockCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class EasyBlockExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, BlockCollection $collection, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->collection = $collection;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_easy_block', [$this, 'renderEasyBlock'], ['is_safe' => ['js', 'html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }

    /**
     * @param array $datas
     */
    public function renderEasyBlock(Environment $env, array $context, $datas, $extra = [])
    {
        $block = $this->em->getRepository($datas["class"])->find($datas["id"]);

        if(!$block || $block->getStatus()){
            return null;
        }

        $blockType = $this->collection->getBlocks()[$block->getType()];

        $event = new GenericEvent(null, ['datas' => $datas, "block" => $block, "blockType" => $blockType, "settings" => array_merge(call_user_func([$blockType, "getDefaultSettings"]), $block->getSettings()) ]);
        /**
         * @var GenericEvent $result;
         */
        $result = $this->eventDispatcher->dispatch($event, "easy_block.render_block");

        $block = $result->getArgument('block');
        $blockType = $result->getArgument('blockType');
        $settings = $result->getArgument('settings');

        return new Markup($this->twig->render($blockType->getTemplate(), array_merge($context, [
            "block" => $block,
            "blockType" => $blockType,
            "settings" => $settings,
        ], $extra)), 'UTF-8');
    }

}
