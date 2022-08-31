<?php

namespace Adeliom\EasyBlockBundle\Controller;

use Adeliom\EasyBlockBundle\Admin\Field\BlockSettingsField;
use Adeliom\EasyBlockBundle\Block\BlockCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;

abstract class BlockCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'easy.block.admin.crud.title.shared_block.'.Crud::PAGE_INDEX)
            ->setPageTitle(Crud::PAGE_EDIT, 'easy.block.admin.crud.title.shared_block.'.Crud::PAGE_EDIT)
            ->setPageTitle(Crud::PAGE_NEW, 'easy.block.admin.crud.title.shared_block.'.Crud::PAGE_NEW)
            ->setPageTitle(Crud::PAGE_DETAIL, 'easy.block.admin.crud.title.shared_block.'.Crud::PAGE_DETAIL)
            ->setEntityLabelInSingular('easy.block.admin.crud.label.shared_block.singular')
            ->setEntityLabelInPlural('easy.block.admin.crud.label.shared_block.plural')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $pages = [Crud::PAGE_INDEX, Crud::PAGE_EDIT, Crud::PAGE_NEW, Crud::PAGE_DETAIL];
        foreach ($pages as $page) {
            $pageActions = $actions->getAsDto($page)->getActions();
            foreach ($pageActions as $action) {
                $action->setLabel('easy.block.admin.crud.label.shared_block.'.$action->getName());
                $actions->remove($page, $action->getAsConfigObject());
                $actions->add($page, $action->getAsConfigObject());
            }
        }

        return $actions;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'easy_block.block_collection' => '?'.BlockCollection::class,
        ]);
    }

    public function new(AdminContext $context): \EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        global $blockType;
        $blockType = $context->getRequest()->query->get('block_type');
        if (!$blockType) {
            $blockCollection = $this->container->get('easy_block.block_collection');

            return $this->render('@EasyBlock/crud/choose_block.html.twig', [
                'blocks' => $blockCollection->getBlocks(),
            ]);
        }

        return parent::new($context);
    }

    public function createEntity(string $entityFqcn)
    {
        global $blockType;
        $entity = new $entityFqcn();
        $entity->setType($blockType);
        $entity->setStatus(true);
        $entity->setSettings(call_user_func([$blockType, 'getDefaultSettings']));

        return $entity;
    }

    public function configureFields(string $pageName): iterable
    {
        global $blockType;

        $context = $this->container->get(AdminContextProvider::class)->getContext();
        $subject = $context->getEntity();

        if ($subject->getInstance() && $subject->getInstance()->getType()) {
            $blockType = $subject->getInstance()->getType();
        }

        if (!empty($blockType)) {
            if (method_exists($blockType, 'configureAdminAssets')) {
                $assets = call_user_func([$blockType, 'configureAdminAssets']);
                if (!empty($assets['js'])) {
                    foreach ($assets['js'] as $file) {
                        $found = false;
                        foreach ($context->getAssets()->getJsAssets() as $assetDto) {
                            if ($assetDto->getValue() === $file) {
                                $found = true;
                            }
                        }

                        if (!$found) {
                            $context->getAssets()->addJsAsset(new AssetDto($file));
                        }
                    }
                }

                if (!empty($assets['css'])) {
                    foreach ($assets['css'] as $file) {
                        $found = false;
                        foreach ($context->getAssets()->getCssAssets() as $assetDto) {
                            if ($assetDto->getValue() === $file) {
                                $found = true;
                            }
                        }

                        if (!$found) {
                            $context->getAssets()->addCssAsset(new AssetDto($file));
                        }
                    }
                }
            }

            if (method_exists($blockType, 'configureAdminFormTheme')) {
                $formThemes = call_user_func([$blockType, 'configureAdminFormTheme']);
                if (!empty($formThemes) && $context->getCrud()) {
                    $context->getCrud()->setFormThemes(array_merge($context->getCrud()->getFormThemes(), $formThemes));
                }
            }
        }

        yield TextField::new('name', 'easy.block.admin.field.name')->setRequired(true)->setColumns(4);
        yield SlugField::new('key', 'easy.block.admin.field.key')->setRequired(true)->setColumns(4)->hideWhenUpdating()->setTargetFieldName('name');
        yield TextField::new('type', 'easy.block.admin.field.type')->setRequired(true)->setColumns(4)->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('status', 'easy.block.admin.field.status')->setColumns(12);

        if ($blockType && in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT])) {
            yield FormField::addPanel('easy.block.admin.field.settings_section');
            yield BlockSettingsField::new('settings', false)->setFormType($blockType);
        }
    }
}
