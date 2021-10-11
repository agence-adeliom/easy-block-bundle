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
     *
     * @var array
     */
    private $assets;

    /**
     * @var array
     */
    private $traces;

    /**
     * @var BlockCollection
     */
    private $collection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $class;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, BlockCollection $collection, EntityManagerInterface $em, string $class, FormFactory  $formFactory)
    {
        $this->twig = $twig;
        $this->collection = $collection;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->formFactory = $formFactory;

        $this->assets = [
            'js' => [],
            'css' => [],
            'webpack' => [],
        ];

        $this->traces = [];
    }

    /**
     * @return array|string
     */
    public function includeAssets()
    {
        $html = '';

        if (!empty($this->assets['css'])){
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
                $html .= "\n" . $this->twig->createTemplate(sprintf("{{ encore_entry_link_tags('%s') }}", $webpack))->render();
                $html .= "\n".$this->twig->createTemplate(sprintf("{{ encore_entry_script_tags('%s') }}", $webpack))->render();
            } catch (LoaderError | SyntaxError $e) {
                $html .= "";
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
     * @param Environment $env
     * @param array $context
     * @param array $datas
     * @param array $extra
     * @return Markup|null
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     */
    public function renderEasyBlock(Environment $env, array $context, $datas, $extra = [])
    {
        $block = null;
        if(is_array($datas)){
            $block = $this->em->getRepository($datas["class"])->find($datas["id"]);
        }

        if(is_string($datas)){
            $block = $this->em->getRepository($this->class)->findOneBy(['key' => $datas]);
        }


        if(!$block || !$block->getStatus()){
            return null;
        }

        $blockType = $this->collection->getBlocks()[$block->getType()];

        $stats = $this->startTracing($block);
        $defaultSetting = call_user_func([$blockType, "getDefaultSettings"]);
        $defaultAssets = call_user_func([$blockType, "configureAssets"]);

        // Tranform settings way 1 : use blockType form transformers
        $blockSettings = $this->transformSettingsWithBlockTypeFormBuild($blockType, $block, $defaultSetting);

        // Tranform settings way 2 : with dispatch / event listeners
        $event = new GenericEvent(null, [
            'datas' => $datas,
            "block" => $block,
            "blockType" => $blockType,
            "settings" => $blockSettings,
            'assets' => $defaultAssets
        ]);

        /**
         * @var GenericEvent $result;
         */
        $result = $this->eventDispatcher->dispatch($event, "easy_block.render_block");

        $block = $result->getArgument('block');
        $blockType = $result->getArgument('blockType');
        $blockDatas = $result->getArgument('settings');

        // Stats
        if(isset($blockDatas["block_type"])){
            unset($blockDatas["block_type"]);
        }

        if(isset($blockDatas["position"])){
            $stats["position"] = $blockDatas["position"];
            unset($blockDatas["position"]);
        }

        $stats["defaultSettings"] = $defaultSetting;
        $stats["settings"] = $blockDatas;
        $stats["extra"] = $extra;
        $stats["type"] = get_class($blockType);
        $stats["assets"] = $result->getArgument('assets') ?: [];

        $this->assets = array_merge($this->assets, $stats["assets"]);
        $this->stopTracing($stats["id"], $stats);

        // Render
        return new Markup($this->twig->render($blockType->getTemplate(), array_merge($context, [
            "block" => $block,
            "blockType" => $blockType,
            "settings" => $blockDatas,
        ], $extra)), 'UTF-8');
    }

    public function transformSettingsWithBlockTypeFormBuild($blockType, $block, $defaultSetting) {

        $formBuilder = $this->formFactory->createBuilder($block->getType(), null, ['csrf_protection' => false]);

        // init blockType form builder
        $blockType->buildBlock($formBuilder, []);

        // Submit to use optionnal form transformers
        $form = $formBuilder->getForm();
        $form->submit(array_merge($defaultSetting, $block->getSettings()));

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
