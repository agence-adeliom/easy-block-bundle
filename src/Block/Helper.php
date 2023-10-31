<?php

namespace Adeliom\EasyBlockBundle\Block;

use Adeliom\EasyBlockBundle\Entity\Block;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class Helper
{
    /**
     * This property is a state variable holdings all assets used by the block for the current PHP request
     * It is used to correctly render the javascripts and stylesheets tags on the main layout.
     */
    private array $assets = [
        'js' => [],
        'css' => [],
        'webpack' => [],
    ];

    private array $traces = [];

    public function __construct(
        /**
         * @readonly
         */
        private Environment $twig,
        /**
         * @readonly
         */
        private EventDispatcherInterface $eventDispatcher,
        /**
         * @readonly
         */
        private BlockCollection $collection,
        /**
         * @readonly
         */
        private EntityManagerInterface $em,
        /**
         * @readonly
         */
        private string $class,
        /**
         * @readonly
         */
        private FormFactory $formFactory
    ) {
    }

    /**
     * @return mixed[]|string
     */
    public function includeAssets(): array|string
    {
        $html = '';

        if (!empty($this->assets['css'])) {
            $html .= "<style media='all'>";

            foreach ($this->assets['css'] as $stylesheet) {
                $html .= "\n".sprintf('@import url(%s);', $stylesheet);
            }

            $html .= "\n</style>";
        }

        foreach ($this->assets['js'] as $javascript) {
            $html .= "\n".sprintf('<script src="%s" type="text/javascript"></script>', $javascript);
        }

        foreach ($this->assets['webpack'] as $webpack) {
            try {
                $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_link_tags('%s') }}", $webpack))->render();
                $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_script_tags('%s') }}", $webpack))->render();
            } catch (LoaderError|SyntaxError) {
                $html .= '';
            }
        }

        return $html;
    }

    /**
     * Returns the rendering traces.
     */
    public function getTraces(): array
    {
        return $this->traces;
    }

    private function startTracing(Block $block): array
    {
        return [
            'id' => uniqid(),
            'name' => $block->getName(),
            'type' => $block->getType(),
            'key' => $block->getKey(),
            'defaultSettings' => [],
            'settings' => [],
            'extra' => [],
            'assets' => [
                'js' => [],
                'css' => [],
                'webpack' => [],
            ],
        ];
    }

    private function stopTracing($id, array $stats): void
    {
        $this->traces[$id] = $stats;
    }

    /**
     * @param array $datas
     * @param array $extra
     *
     * @return Markup|null
     *
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     */
    public function renderEasyBlock(Environment $env, array $context, $datas, $extra = [])
    {
        $block = null;
        if (is_array($datas)) {
            $block = $this->em->getRepository($datas['class'])->find($datas['id']);
        }

        if (is_string($datas)) {
            if (is_numeric($datas)) {
                $block = $this->em->getRepository($this->class)->findOneBy(['id' => $datas]);
            } else {
                $block = $this->em->getRepository($this->class)->findOneBy(['key' => $datas]);
            }
        }

        if (!$block || !$block->getStatus()) {
            return null;
        }

        $blockType = $this->collection->getBlocks()[$block->getType()];

        $stats = $this->startTracing($block);
        $defaultSetting = call_user_func([$blockType, 'getDefaultSettings']);
        $defaultAssets = call_user_func([$blockType, 'configureAssets']);

        // Tranform settings way 1 : use blockType form transformers
        $blockSettings = $this->transformSettingsWithBlockTypeFormBuild($blockType, $block, $defaultSetting);

        // Add a way to automatically set an ID (base on loop index when the page is rendered)
        if (empty($blockSettings['attr_id'])) {
            global $blockLoopIndex;
            if (empty($blockLoopIndex)) {
                $blockLoopIndex = 0;
            }

            ++$blockLoopIndex;
            $blockSettings['attr_id'] = 'block-'.$blockLoopIndex;
        }

        // Tranform settings way 2 : with dispatch / event listeners
        $event = new GenericEvent(null, [
            'datas' => $datas,
            'block' => $block,
            'blockType' => $blockType,
            'settings' => $blockSettings,
            'assets' => $defaultAssets,
        ]);

        /**
         * @var GenericEvent $result;
         */
        $result = $this->eventDispatcher->dispatch($event, 'easy_block.render_block');

        $block = $result->getArgument('block');
        $blockType = $result->getArgument('blockType');
        $blockDatas = $result->getArgument('settings');

        // Stats
        if (isset($blockDatas['block_type'])) {
            unset($blockDatas['block_type']);
        }

        if (isset($blockDatas['position'])) {
            $stats['position'] = $blockDatas['position'];
            unset($blockDatas['position']);
        }

        $event = new GenericEvent(null, [
            'defaultSettings' => $defaultSetting,
            'settings' => $blockDatas,
            'extra' => $extra,
            'type' => $blockType::class,
            'assets' => $result->getArgument('assets') ?: [],
        ]);

        /**
         * @var GenericEvent $result;
         */
        $statsEvent = $this->eventDispatcher->dispatch($event, 'easy_block.tracing.settings');

        $stats['defaultSettings'] = $statsEvent->getArgument('defaultSettings');
        $stats['settings'] = $statsEvent->getArgument('settings');
        $stats['extra'] = $statsEvent->getArgument('extra');
        $stats['type'] = $statsEvent->getArgument('type');
        $stats['assets'] = $statsEvent->getArgument('assets');

        $this->assets = array_merge_recursive($this->assets, $stats['assets']);

        $this->stopTracing($stats['id'], $stats);

        // Render
        return new Markup($this->twig->render($blockType->getTemplate(), array_merge($context, [
            'block' => $block,
            'blockType' => $blockType,
            'settings' => $blockDatas,
        ], $extra)), 'UTF-8');
    }

    public function transformSettingsWithBlockTypeFormBuild($blockType, $block, $defaultSetting)
    {
        $formBuilder = $this->formFactory->createBuilder($block->getType(), null, ['csrf_protection' => false]);

        // init blockType form builder
        $blockType->buildBlock($formBuilder, []);

        // Submit to use optionnal form transformers
        $form = $formBuilder->getForm();
        $form->setData(array_merge($defaultSetting, $block->getSettings()));

        // Put norm datas into block settings
        // norm data are transfomed data
        $blockSettings = $form->getNormData();
        if (!empty($form->getNormData())) {
            foreach ($form->getNormData() as $field => $value) {
                if (!empty($form->get($field))) {
                    $blockSettings[$field] = $form->get($field)->getNormData();
                }
            }
        }

        return $blockSettings;
    }
}
